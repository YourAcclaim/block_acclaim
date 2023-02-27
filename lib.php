<?php
// This file is part of Credly's Acclaim Moodle Block Plugin
//
// Credly's Acclaim Moodle Block Plugin is free software: you can redistribute it
// and/or modify it under the terms of the MIT license as published by
// the Free Software Foundation.
//
// This script is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// MIT License for more details.
//
// You can find the GNU General Public License at <https://opensource.org/licenses/MIT>.

/**
 * Credly's Acclaim Moodle Block Plugin
 * Credly: http://youracclaim.com
 * Moodle: http://moodle.org/
 *
 * Utility functions for the plugin.
 *
 * @package    block_acclaim
 * @copyright  2020 Credly, Inc. <http://youracclaim.com>
 * @license    https://opensource.org/licenses/MIT
 */
require_once(__DIR__ . '/../../config.php');

class block_acclaim_lib {
    public static $config = null;
    public static $allow_print = true;
    public static $allow_debug = false;

    public static function config() {
        if (!isset(self::$config)) {
            self::$config = get_config('block_acclaim');
        }
        return self::$config;
    }

    public function __construct() {
        self::config();
    }

    /**
     * Queue up a badge to be issued by Acclaim.
     *
     * @param stdClass $event
     */
    public function create_pending_badge($course_id, $user_id) {
        global $DB;
        $course = $DB->get_record('block_acclaim_courses', array('courseid' => $course_id), '*');
        if (!$course) {
            // This method can be called outside of Acclaim context (for example, by unit tests)
            // with a non-Acclaim course.
            return;
        }
        $user = $DB->get_record('user', array('id' => $user_id), '*', MUST_EXIST);

        $pending_badge = new stdClass();
        $pending_badge->badgetemplateid = $course->badgeid;
        $pending_badge->firstname = $user->firstname;
        $pending_badge->lastname = $user->lastname;
        $pending_badge->expiration = $course->expiration;
        $pending_badge->recipientemail = $user->email;

        $DB->insert_record('block_acclaim_pending_badges', $pending_badge);
    }

    /**
     * Issue all pending badges through Acclaim.
     *
     * @param curl $curl - The curl http library.
     * @return http code
     */
    public function issue_pending_badges($curl) {
        global $DB;
        $config = self::$config;
        $datetime = $this->convert_time_stamp(time());
        $url = "{$config->url}/organizations/{$config->org}/badges";

        $pending_badges = $DB->get_records('block_acclaim_pending_badges');

        if (self::$allow_debug) {
            print_r($pending_badges);
        }

        foreach ($pending_badges as &$badge) {
            // Output goes to the cron log.
            if (self::$allow_print) {
                print "Acclaim block: Issuing badge {$badge->badgetemplateid} to {$badge->recipientemail}.\n";
            }


            $payload = [
                'badge_template_id' => $badge->badgetemplateid,
                'issued_to_first_name' => $badge->firstname,
                'issued_to_last_name' => $badge->lastname,
                'recipient_email' => $badge->recipientemail,
                'issued_at' => $datetime
            ];

            if ($badge->expiration) {
                $payload['expires_at'] = $this->convert_time_stamp($badge->expiration);
            }

            $curl->post(
                $url, $payload, array('CURLOPT_USERPWD' => block_acclaim_lib::$config->token . ':')
            );

            print $curl->info['http_code'];

            if ($curl->info['http_code'] == 201) {
                // The badge has been issued so we remove it from pending.
                $DB->delete_records('block_acclaim_pending_badges',  array('id' => $badge->id));
                if (self::$allow_print) {
                    print "Acclaim block: Success.\n";
                }
            } elseif ($curl->info['http_code'] == 422) {
                // Acclaim can not issue the badge so we remove this from pending
                // so it will not try again.  This could happen for example if the
                // user already has been issued a badge.
                if (self::$allow_print) {
                    print "Acclaim block: Failed. Error 422. This task will not be re-tried.\n";
                    error_log('block_acclaim:');
                    error_log(print_r($curl->response, true));
                }
                $DB->delete_records('block_acclaim_pending_badges', array('id' => $badge->id));
            } else {
                // some other issue is preventing the badge from being issued
                // for example site down or token incorrectly entered.  The
                // record is left as pending to try again in the future.
                if (self::$allow_print) {
                    print "Acclaim block: Failed. Try again later.\n";
                    error_log('block_acclaim:');
                    error_log(print_r($curl->response, true));
                }
            }
        };

        return $curl->info['http_code'];
    }

    /**
     * Get any field from a course.
     *
     * @param string $course_id
     * @param string $field
     * @return string
     */
    function get_course_info($course_id, $field) {
        global $DB;
        $course = $DB->get_record('block_acclaim_courses', array('courseid' => $course_id));
        return empty($course) ? '' : $course->$field;
    }

    /**
     * Add course information to the form, and save it to the database.
     *
     * @param block_acclaim_form $fromform - The block data for the course.
     * @return stdClass - The inserted database record.
     */
    function set_course_badge_template($fromform) {
        global $DB;

        $badge_data = json_decode($fromform->badgename, true);
        $badge_name = $badge_data[$fromform->badgeid];

        $urlId = $fromform->badgeid.'_url';
        $badge_url = $badge_data[$urlId];

        if (isset($badge_name)) {
            $fromform->badgename = $badge_name;
        }

        if (isset($badge_url)) {
            $fromform->badgeurl = $badge_url;
        } else {
          $fromform->badgeurl = "Badge URL Isn't Set";
        }

        $DB->delete_records('block_acclaim_courses',  array('courseid' => $fromform->courseid));

        return $DB->insert_record('block_acclaim_courses', $fromform);
    }

    /**
     * Get all badges.
     *
     * @return array
     */
    function badge_names($returntype = '') {
        $badge_items = array();
        $badge_urls = array();

        $json = $this->query_api(null);
        $this->accumulate_badge_names($json, $badge_items, $badge_urls);

        $next_page_url = '';
        if (isset($json['metadata'])) {
            $metadata = $json['metadata'];
            $next_page_url = $metadata['next_page_url'];

            while (!is_null($next_page_url)) {
                $json = $this->query_api("$next_page_url&sort=name&filter=state::active");
                $this->accumulate_badge_names($json, $badge_items, $badge_urls);

                if (isset($json['metadata'])) {
                    $metadata = $json['metadata'];
                    $next_page_url = $metadata['next_page_url'];
                }
            }
        }
        if ($returntype == 'urls') {
          return $badge_urls;
        }
        return $badge_items;
    }

    ////////////////////
    // Private functions
    ////////////////////

    /**
     * Get the badge list from the Acclaim API, as JSON.
     *
     * @param curl $curl
     * @param string $url
     * @param string $token
     * @return object
     */
    private function fetch_badge_json($curl, $url, $token) {
        $params = array('sort' => 'name', 'filter' => 'state::active');
        $options = array('CURLOPT_USERPWD' => $token . ':');
        return $curl->get($url, $params, $options);
    }

    /**
     * Create a user-readable list of badge names.
     *
     * @param object $json - Badge list JSON from the Acclaim API.
     * @param array $badge_names - An accumulated list of badge names.
     */
    private function accumulate_badge_names($json, &$badge_items, &$badge_urls) {
        $arr = json_decode($json, true);
        if (isset($arr['data'])) {
            foreach ($arr['data'] as $item) {
                $len = strlen($item['name']);
                $badge_items[$item['id']] = $len > 75 ? substr($item['name'], 0, 75 - $len) . '...' : $item['name'];
                $badge_urls[$item['id'].'_url'] = $item['url'];
            }
        } else {
            error_log('invalid api, token or unable to connect');
        }
    }

    /**
     * Send a request to Credly's Acclaim API.
     *
     * @param string $url - The URL to send. If omitted, request all badge templates for the
     *   configured organization.
     * @return object - The JSON response.
     */
    private function query_api($url) {
        if (is_null($url)) {
            $config = self::$config;
            $url = "{$config->url}/organizations/{$config->org}/badge_templates?sort=name&filter=state::active";
        }

        $params = array('sort' => 'name', 'filter' => 'state::active');
        $options = array('CURLOPT_USERPWD' => self::$config->token . ':');
        $result = (new curl())->get($url, $params, $options);
        return $result;
    }

    /**
     * Create a timestamp readable by the Acclaim API.
     *
     * @param string $timestamp
     * @return string
     */
    protected function convert_time_stamp($timestamp) {
        return $timestamp ? gmdate('Y-m-d  h:i:s a', $timestamp) : null;
    }
}
