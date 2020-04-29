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
 * PHPUnit data generator tests. Do not change the capitalization of this file name. phpunit expects files
 * of the form "*Test.php"
 *
 * @package    block_acclaim
 * @copyright  2020 Credly, Inc. <http://youracclaim.com>
 * @license    https://opensource.org/licenses/MIT
 */
GLOBAL $CFG;

defined('MOODLE_INTERNAL') || die();

require_once(dirname(__FILE__) . '/../../../config.php');
require_once($CFG->dirroot . '/blocks/acclaim/lib.php');

class block_acclaim_lib_test extends advanced_testcase {
    private $acclaim = null;

    function setUp() {
        $this->resetAfterTest(true);

        $this->acclaim = new block_acclaim_lib();
        block_acclaim_lib::$config = new stdClass();
        block_acclaim_lib::$config->url = 'url';
        block_acclaim_lib::$config->token = 'token';
        block_acclaim_lib::$config->org = '123';
        block_acclaim_lib::$allow_print = false;
    }

    ////////////////////
    // Tests
    ////////////////////

    public function test_accumulate_badge_names() {
        $this->acclaim = new block_acclaim_lib();
        $badge_items = array();
        $json = json_encode(array('data' => array(0 => array('id' => 1, 'name' => 'johnny'))));
        $this->invokePrivate('accumulate_badge_names', array($json, &$badge_items));
        $this->assertEquals(array(1 => 'johnny'), $badge_items);
        $json = json_encode(array('data' => array(0 => array('id' => 2, 'name' => 'billy'))));
        $this->invokePrivate('accumulate_badge_names', array($json, &$badge_items));
        $this->assertEquals(array(1 => 'johnny', 2 => 'billy'), $badge_items);
    }

    public function test_create_pending_badge() {
        global $DB;
        $this->acclaim = new block_acclaim_lib();

        $form = $this->mock_form();
        $form->badgename = 'some name';
        $DB->insert_record('block_acclaim_courses', $form);

        $event = $this->mock_event();
        $this->acclaim->create_pending_badge($event->courseid, $event->relateduserid);

        $count = $DB->count_records('block_acclaim_pending_badges');
        $this->assertEquals(1, $count);

        $pending_badges = $DB->get_records_list(
            'block_acclaim_pending_badges',
            'badgetemplateid',
            array('1edb816d-a9fb-445d-b024-bb52075718e5')
        );
        $pending_badge = array_pop($pending_badges);

        $this->assertEquals('Richard', $pending_badge->firstname);
        $this->assertEquals('Kimble', $pending_badge->lastname);
        $this->assertEquals('richard.kimble@fugative.me', $pending_badge->recipientemail);
        $this->assertEquals(0, $pending_badge->expiration);
    }

    public function test_set_course_badge_template_is_unique() {
        global $DB;

        $result = $this->acclaim->set_course_badge_template($this->mock_form());
        $count = $DB->count_records_select('block_acclaim_courses', "courseid = '38'");
        $this->assertEquals($count, 1);

        $result = $this->acclaim->set_course_badge_template($this->mock_form());
        $count = $DB->count_records_select('block_acclaim_courses', "courseid = '38'");
        $this->assertEquals(1, $count);
    }

    public function test_get_course_info() {
        global $DB;
        $table = 'block_acclaim_courses';
        $DB->delete_records($table);
        $this->assertEmpty($DB->get_records($table));

        $event = $this->mock_event('2');

        $dataobject = new stdClass();
        $dataobject->badgeid = '919309fc-648c-42cb-9415-7f8ecf2f681f';
        $dataobject->courseid = $event->courseid;
        $dataobject->expiration = 0;
        $dataobject->badgename = 'test';
        $DB->insert_record($table, $dataobject, $returnid=true, $bulk=false);

        $badge_id = $this->acclaim->get_course_info($event->courseid, 'badgeid');
        $this->assertEquals($dataobject->badgeid,$badge_id);
    }

    public function test_issue_pending_badges_success() {
        global $DB;
        $url = 'url/organizations/123/badges';

        $this->assert_pending_badge_count(0);
        $pending_badge = $this->create_pending_badge();
        $DB->insert_record('block_acclaim_pending_badges', $pending_badge);
        $this->assert_pending_badge_count(1);
        $mock_curl = $this->assert_curl(201, $url);
        $return_code = $this->acclaim->issue_pending_badges($mock_curl);
        $this->assertEquals(201, $return_code);
        $this->assert_pending_badge_count(0);
    }

    public function test_issue_pending_badges_success_with_expiration() {
        global $DB;
        $time = time();
        $url = 'url/organizations/123/badges';

        $this->assert_pending_badge_count(0);
        $pending_badge = $this->create_pending_badge($time);
        $DB->insert_record('block_acclaim_pending_badges', $pending_badge);
        $this->assert_pending_badge_count(1);
        $mock_curl = $this->assert_curl(201, $url, $time);
        $return_code = $this->acclaim->issue_pending_badges($mock_curl);
        $this->assertEquals(201, $return_code);
        $this->assert_pending_badge_count(0);
    }

    public function test_issue_pending_badges_unprocessable_entity() {
        global $DB;
        $url = 'url/organizations/123/badges';

        $this->assert_pending_badge_count(0);
        $pending_badge = $this->create_pending_badge();
        $DB->insert_record('block_acclaim_pending_badges', $pending_badge);
        $this->assert_pending_badge_count(1);

        $mock_curl = $this->assert_curl(422, $url);
        $return_code = $this->acclaim->issue_pending_badges($mock_curl);
        $this->assertEquals(422, $return_code);
        $this->assert_pending_badge_count(0);
    }

    public function test_issue_pending_badges_failure() {
        global $DB;
        $url = 'url/organizations/123/badges';

        $this->assert_pending_badge_count(0);
        $pending_badge = $this->create_pending_badge();
        $DB->insert_record('block_acclaim_pending_badges', $pending_badge);
        $this->assert_pending_badge_count(1);

        $mock_curl = $this->assert_curl(401, $url);
        $return_code = $this->acclaim->issue_pending_badges($mock_curl);
        $this->assertEquals(401, $return_code);
        $this->assert_pending_badge_count(1);
    }

    public function test_issue_pending_badges_mutltiple() {
        global $DB;
        $url = 'url/organizations/123/badges';

        $this->assert_pending_badge_count(0);
        $pending_badge = $this->create_pending_badge();
        $DB->insert_record('block_acclaim_pending_badges', $pending_badge);
        $DB->insert_record('block_acclaim_pending_badges', $pending_badge);
        $this->assert_pending_badge_count(2);

        $mock_curl = $this->assert_curl(201, $url, 0, 2);
        $return_code = $this->acclaim->issue_pending_badges($mock_curl);
        $this->assertEquals(201, $return_code);

        $this->assert_pending_badge_count(0);
    }
   
    ////////////////////
    // Private functions
    ////////////////////

    /**
     * Create a fake Moodle event.
     *
     * @return stdClass
     */
    private function mock_event() {
        global $DB;
        $user = new stdClass();
        $user->firstname = 'Richard';
        $user->lastname = 'Kimble';
        $user->email = 'richard.kimble@fugative.me';
        $user_id = $DB->insert_record('user', $user);

        $event = new stdClass();
        $event->eventname = ' \core\event\course_completed';
        $event->component = 'core';
        $event->action = 'completed';
        $event->target = 'course';
        $event->objecttable = 'course_completions';
        $event->objectid = '23';
        $event->crud = 'u';
        $event->edulevel = '2';
        $event->contextid = '147';
        $event->contextlevel = '50';
        $event->contextinstanceid = '14';
        $event->userid = 2;
        $event->courseid = '38';
        $event->relateduserid = $user_id;
        $event->anonymous = '0';
        $event->timecreated = '1409543537';
        return $event;
    }

    /**
     * Create a fake block_acclaim_form.
     *
     * @return stdClass
     */
    private function mock_form() {
        $badgenames = [
            '123' => 'mos',
            '1edb816d-a9fb-445d-b024-bb52075718e5' => 'def',
        ];

        $json = json_encode($badgenames);

        $fromform = new stdClass();
        $fromform->badgeid = '1edb816d-a9fb-445d-b024-bb52075718e5';
        $fromform->expiration = 0;
        $fromform->blockid = '';
        $fromform->courseid = '38';
        $fromform->badgename = $json;
        $fromform->submitbutton = 'Save changes';

        return $fromform;
    }

    /**
     * Create a pending badge object.
     *
     * @param int $expiration - The expiration date.
     * @return stdClass
     */
    private function create_pending_badge($expiration = 0) {
        $pending_badge = new stdClass();
        $pending_badge->badgetemplateid = '123';
        $pending_badge->firstname = 'Richard';
        $pending_badge->lastname = 'Kimble';
        $pending_badge->expiration = $expiration;
        $pending_badge->recipientemail = 'richard.kimble@fugative.me';
        return $pending_badge;
    }

    /**
     * Verify that curl was called with the given parameters.
     *
     * @param string $return_code - Expect this HTTP return code.
     * @param string $url - Expect this url.
     * @param int $expires_at - Expect this expiration timestamp.
     * @param int $times - Expect it to have been called this many times.
     * @return string
     */
    private function assert_curl($return_code, $url, $expires_at = 0, $times = 1) {
        $expected_payload = [
            'badge_template_id' => '123',
            'issued_to_first_name' => 'Richard',
            'issued_to_last_name' => 'Kimble',
            'recipient_email' => 'richard.kimble@fugative.me',
            'issued_at' => $this->convert_time_stamp(time())
        ];

        if ($expires_at != 0) {
            $expected_payload['expires_at'] =
                $this->convert_time_stamp($expires_at);
        }

        $mock_curl = $this->getMockBuilder(curl::class)
            ->setMethods(['post'])
            ->getMock();

        $mock_curl->info = array('http_code' => $return_code);
        $mock_curl->expects($this->exactly( $times ))
            ->method('post')
            ->with(
                $url,
                $expected_payload,
                array('CURLOPT_USERPWD' => block_acclaim_lib::$config->token . ':')
            );
        return $mock_curl;
    }

    /**
     * Verify that the given number of pending badges have been added to the database.
     *
     * @param string $expected_count - The number of expected badges.
     * @return string
     */
    private function assert_pending_badge_count($expected_count) {
        global $DB;
        $count = $DB->count_records('block_acclaim_pending_badges');
        $this->assertEquals($expected_count, $count);
    }

    /**
     * Create a timestamp readable by the Acclaim API.
     *
     * @param string $timestamp
     * @return string
     */
    private function convert_time_stamp($timestamp) {
        return $timestamp ? gmdate('Y-m-d  h:i:s a', $timestamp) : null;
    }

    /**
     * Call a protected/private method of the Acclaim class.
     *
     * @param string $methodName - Method name to call.
     * @param array $parameters - Array of parameters to pass into the method.
     * @return mixed Method return.
     */
    private function invokePrivate($methodName, array $parameters = array()) {
        $reflection = new \ReflectionClass(block_acclaim_lib::class);
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);
        return $method->invokeArgs($this->acclaim, $parameters);
    }
}
