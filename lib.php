<?php

// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
* @package    block_acclaim
* @copyright  2014 Yancy Ribbens <yancy.ribbens@gmail.com>
* @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
*/

require_once(dirname(__FILE__).'/../../config.php');

function block_acclaim_query_acclaim_api($customUrl)
{
    $config = get_config('block_acclaim');
    $url="{$config->url}/organizations/{$config->org}/badge_templates?sort=name&filter=state::active";
    if (!is_null($customUrl)) {
    	$url = $customUrl;
    }
    $username=$config->token;
    $password = "";
    $curl = new curl;
    return block_acclaim_return_json_badges($curl, $url, $username);
}

function block_acclaim_truncate($input)
{
    $max_length = 75;

    if(strlen($input) > $max_length){
        $len = strlen($input);
        $trim_amount = ($max_length - $len);
        return substr($input,0, $trim_amount)."...";
        }

    return $input;
}

function block_acclaim_get_badge_info($course_id,$field)
{
    global $DB;
    $return_val = "";

    $course = $DB->get_record('block_acclaim_courses', array('courseid' => $course_id));

    if(!empty($course)){
        $return_val = $course->$field;
    }

    return $return_val;
}

function block_acclaim_get_block_course($course_id)
{
    global $DB;
    $course = $DB->get_record('block_acclaim_courses', array('courseid' => $course_id), '*', MUST_EXIST);
    return $course;
}

function block_acclaim_write_badge_to_issue($fromform)
{
    global $DB;

    $fromform = block_acclaim_update_form_with_badge_name($fromform);
    $DB->delete_records('block_acclaim_courses',  array('courseid' => $fromform->courseid));

    return $DB->insert_record('block_acclaim_courses', $fromform);
}

function block_acclaim_get_issue_badge_url()
{
    $block_acclaim_config = get_config('block_acclaim');

    $base_url = $block_acclaim_config->url;
    $org_id = $block_acclaim_config->org;
    $request_url = "{$base_url}/organizations/{$org_id}/badges";
    return $request_url;
}

function block_acclaim_get_request_token()
{
    $block_acclaim_config = get_config('block_acclaim');
    return $block_acclaim_config->token;
}

function block_acclaim_return_user($user_id)
{
    global $DB;
    return $DB->get_record('user', array('id'=>$user_id), '*', MUST_EXIST);
}

function block_acclaim_convert_time_stamp($timestamp)
{
    if($timestamp){
        return gmdate("Y-m-d  h:i:s a", $timestamp);
    }

    return $timestamp;
}

function block_acclaim_update_form_with_badge_name($fromform)
{
    $all_badges_names = json_decode($fromform->badgename);
    $badge_id = $fromform->badgeid;

    if(isset($all_badges_names->$badge_id)){
        $badge_name =  $all_badges_names->$badge_id;
        $fromform->badgename = $badge_name;
    }

    return $fromform;
}

function block_acclaim_create_pending_badge($event)
{
    global $DB;
    $course = block_acclaim_get_block_course($event->courseid);
    $pending_badge = block_acclaim_create_pending_badge_obj($event, $course);
    $DB->insert_record('block_acclaim_pending_badges', $pending_badge);
}

function block_acclaim_create_pending_badge_obj($event, $course)
{
    $user_id = $event->relateduserid;
    $badge_template_id = $course->badgeid;
    $course_id = $event->courseid;
    $user = block_acclaim_return_user($user_id);
    $firstname = $user->firstname;
    $lastname = $user->lastname;
    $email = $user->email;

    $expires_at = "";
    if($course->expiration){
        $expires_at = block_acclaim_convert_time_stamp(
            $course->expiration
        );
    }

    $pending_badge = new stdClass();
    $pending_badge->badgetemplateid = $badge_template_id;
    $pending_badge->firstname = $firstname;
    $pending_badge->lastname = $lastname;
    $pending_badge->expiration = $expires_at;
    $pending_badge->recipientemail = $email;

    return $pending_badge;
}

function block_acclaim_issue_badge($curl, $time, $url, $token){
    global $DB;

    $datetime = block_acclaim_convert_time_stamp($time);

    $pending_badges = $DB->get_records('block_acclaim_pending_badges');

	foreach ($pending_badges as &$badge) {

        $payload = [
            'badge_template_id' => $badge->badgetemplateid,
            'issued_to_first_name' => $badge->firstname,
            'issued_to_last_name' => $badge->lastname,
            'recipient_email' => $badge->recipientemail,
            'issued_at' => $datetime
        ];

        $curl->post(
            $url, $payload, array( "CURLOPT_USERPWD" => $token. ":" )
        );

        if ($curl->info["http_code"] == 201) {
            // The badge has been issued so we remove it from pending.
            $DB->delete_records('block_acclaim_pending_badges',  array('id' => $badge->id));
        } elseif ($curl->info["http_code"] == 422) {
            // Acclaim can not issue the badge so we remove this from pending
            // so it will not try again.  This could happen for example if the
            // user already has been issued a badge.
            error_log(print_r($curl->response, true));
            $DB->delete_records('block_acclaim_pending_badges',  array('id' => $badge->id));
        } else {
            // some other issue is preventing the badge from being issued
            // for example site down or token incorrectly entered.  The
            // record is left as pending to try again in the future.
            error_log(print_r($curl->response, true));
        }
    };

    return $curl->info["http_code"];
}

function block_acclaim_return_json_badges($curl, $url, $token)
{
    $params = array("sort" => "name", "filter" => "state::active");
    $options = array("CURLOPT_USERPWD" => $token . ":");

    $http = $curl->get($url, $params, $options);
    return $http;
}

function block_acclaim_build_radio_buttons($json, $badge_items)
{
    $arr = json_decode($json, true);
    if (isset($arr['data'])) {
        foreach ($arr['data'] as $item) {
            $friendly_name = block_acclaim_truncate($item["name"]);
            $badge_id = $item["id"];
            $badge_items[$badge_id] = $friendly_name;
        }
    } else {
        error_log("invalid api, token or unable to connect");
    }
    return $badge_items;
}

function block_acclaim_images()
{
    $badge_items = array();
	
    $json = block_acclaim_query_acclaim_api(null);
    $badge_items = block_acclaim_build_radio_buttons($json, $badge_items);
    
    $next_page_url = "";
    if (isset($json['metadata'])) {
    	$metadata = $json['metadata'];
    	$next_page_url = $metadata["next_page_url"];
    	
    	while (!is_null($next_page_url)) {
                $json = block_acclaim_query_acclaim_api($next_page_url."&sort=name&filter=state::active");
                $badge_items = block_acclaim_build_radio_buttons($json, $badge_items);
    		
    		if (isset($json['metadata'])) {
    			$metadata = $json['metadata'];
    			$next_page_url = $metadata["next_page_url"];
    		}
    	}
    }
    return $badge_items;
}
