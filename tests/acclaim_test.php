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
 * PHPUnit data generator tests
 *
 * @package    block_acclaims
 * @category   phpunit
 * @copyright  2014 Yancy Ribbens <yancy.ribbens@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
GLOBAL $CFG;

defined('MOODLE_INTERNAL') || die();

require_once(dirname(__FILE__) . '/../../../config.php');
require_once($CFG->dirroot . '/blocks/acclaim/lib.php');

class acclaim_lib_test extends advanced_testcase{
    function setUp(){
        $this->resetAfterTest(true);
    }

    public function mock_event($id)
    {
        $event = new stdClass();
        $event->eventname = " \core\event\course_completed";
        $event->component = "core";
        $event->action = "completed";
        $event->target = "course";
        $event->objecttable = "course_completions";
        $event->objectid = "23";
        $event->crud = "u";
        $event->edulevel = "2";
        $event->contextid = "147";
        $event->contextlevel = "50";
        $event->contextinstanceid = "14";
        $event->userid = 2;
        $event->courseid = "123";
        $event->relateduserid = $id;
        $event->anonymous = "0";
        $event->timecreated = "1409543537";
        return $event;
    }

    private function mock_form()
    {
        $badgenames = [
            "123" => "mos",
            "1edb816d-a9fb-445d-b024-bb52075718e5" => "def",
            ];

        $json = json_encode($badgenames);

        $fromform = new stdClass();
        $fromform->badgeid = "1edb816d-a9fb-445d-b024-bb52075718e5";
        $fromform->expiration = 0;
        $fromform->blockid = "";
        $fromform->courseid = "38";
        $fromform->badgename = $json;
        $fromform->submitbutton = "Save changes";

        return $fromform;
    }

    private function create_pending_badge()
    {
        $pending_badge = new stdClass();
        $pending_badge->badgetemplateid = '123';
        $pending_badge->firstname = 'Richard';
        $pending_badge->lastname = 'Kimble';
        $pending_badge->expiration = 0;
        $pending_badge->recipientemail = 'richard.kimble@fugative.me';
        return $pending_badge;
    }

    private function assert_curl($return_code, $url, $token, $time, $times = 1){
        $expected_payload = [
            'badge_template_id' => '123',
            'issued_to_first_name' => 'Richard',
            'issued_to_last_name' => 'Kimble',
            'recipient_email' => 'richard.kimble@fugative.me',
            'issued_at' => block_acclaim_convert_time_stamp($time)
        ];

        $mock_curl = $this->getMockBuilder(curl::class)
            ->setMethods(['post'])
            ->getMock();

        $mock_curl->info = array("http_code" => $return_code);
        $mock_curl->expects($this->exactly( $times ))
            ->method('post')
            ->with(
                $url,
                $expected_payload,
                array("CURLOPT_USERPWD" => $token.":")
            );
        return $mock_curl;
    }

    private function assert_pending_badge_count($expected_count){
        global $DB;
        $count = $DB->count_records('block_acclaim_pending_badges');
        $this->assertEquals($expected_count, $count);
    }

    public function test_get_badge_id()
    {
        global $DB;
        $table = 'block_acclaim';
        $DB->delete_records($table);
        $this->assertEmpty($DB->get_records($table));

        $event = $this->mock_event('2');
        
        $dataobject = new stdClass();
        $dataobject->badgeid = '919309fc-648c-42cb-9415-7f8ecf2f681f';
        $dataobject->courseid = $event->courseid;
        $dataobject->expiration = 0;
        $dataobject->badgename = "test";
        $DB->insert_record($table, $dataobject, $returnid=true, $bulk=false);
        
        $badge_id = block_acclaim_get_badge_info($event->courseid,"badgeid");
        $this->assertEquals($dataobject->badgeid,$badge_id);
   }

   public function test_get_course()
   {
        global $DB;
        $table = 'block_acclaim';
        $DB->delete_records($table);
        $this->assertEmpty($DB->get_records($table));

        $event = $this->mock_event('2');

        $dataobject = new stdClass();
        $dataobject->badgeid = '919309fc-648c-42cb-9415-7f8ecf2f681f';
        $dataobject->courseid = $event->courseid;
        $dataobject->expiration = 1409643600;
        $dataobject->badgename = "test";
        $DB->insert_record($table, $dataobject, $returnid=true, $bulk=false);

        $course = block_acclaim_get_block_course($event->courseid);
        $this->assertEquals($dataobject->expiration,$course->expiration);
        $this->assertEquals($dataobject->badgeid,$course->badgeid);
   }

   public function test_write_block_record()
   {
       global $DB;
       $table = 'block_acclaim';
       $DB->delete_records($table);
       $this->assertEmpty($DB->get_records($table));
       $fromform = $this->mock_form();

       $result = block_acclaim_write_badge_to_issue($fromform);
       $count = $DB->count_records_select($table, "courseid = '38'");
       
       $this->assertEquals($count,1);

       $result = block_acclaim_write_badge_to_issue($fromform);
       $count = $DB->count_records_select($table, "courseid = '38'");

       $this->assertEquals(1,$count);
   }

   public function test_create_pending()
   {
        global $DB;
        //create a user
        $user = new stdClass();
        $user->firstname = "Richard";
        $user->lastname = "Kimble";
        $user->email = "richard.kimble@fugative.me";
        $id = $DB->insert_record('user', $user, $returnid=true, $bulk=false);

        //create and event that will fire with the user id as relateduserid
        $event = $this->mock_event($id);

        //create a course with a badge_template
        $dataobject = new stdClass();
        $dataobject->badgeid = '919309fc-648c-42cb-9415-7f8ecf2f681f';
        $dataobject->courseid = $event->courseid;
        $dataobject->expiration = 0;
        $dataobject->badgename = "test";
        $DB->insert_record('block_acclaim', $dataobject, $returnid=true, $bulk=false);

        $count = $DB->count_records('block_acclaim_pending_badges');
        $this->assertEquals(0, $count);
        block_acclaim_create_pending_badge($event);
        $count = $DB->count_records('block_acclaim_pending_badges');
        $this->assertEquals(1, $count);

        $pending_badges = $DB->get_records_list('block_acclaim_pending_badges', 'badgetemplateid', array('919309fc-648c-42cb-9415-7f8ecf2f681f'));
        $pending_badge = array_pop($pending_badges);

        $this->assertEquals($user->firstname, $pending_badge->firstname);
        $this->assertEquals($user->lastname, $pending_badge->lastname);
        $this->assertEquals($user->email, $pending_badge->recipientemail);
   }

   public function test_build_radio_buttons()
   {
        $badge_items = array();
        $json = json_encode(array('data' => array(0 => array('id' => 1, 'name' => 'johnny'))));
        $badge_items = block_acclaim_build_radio_buttons($json, $badge_items);
        $this->assertEquals(array( 1 => "johnny"), $badge_items);
   }

    public function test_issue_badge_success(){
        global $DB;
        $time = time();
        $url = 'url';
        $token = 'token';

        $this->assert_pending_badge_count(0);
        $pending_badge = $this->create_pending_badge();
        $DB->insert_record('block_acclaim_pending_badges', $pending_badge);
        $this->assert_pending_badge_count(1);
        $mock_curl = $this->assert_curl(201, $url, $token, $time);
        $return_code = block_acclaim_issue_badge(
            $mock_curl, $time, $url, $token);
        $this->assertEquals(201, $return_code);
        $this->assert_pending_badge_count(0);
   }

    public function test_issue_badge_unprocessable_entity(){
        global $DB;
        $time = time();
        $url = 'url';
        $token = 'token';

        $this->assert_pending_badge_count(0);
        $pending_badge = $this->create_pending_badge();
        $DB->insert_record('block_acclaim_pending_badges', $pending_badge);
        $this->assert_pending_badge_count(1);

        $mock_curl = $this->assert_curl(422, $url, $token, $time);
        $return_code = block_acclaim_issue_badge(
            $mock_curl, $time, $url, $token);
        $this->assertEquals(422, $return_code);
        $this->assert_pending_badge_count(0);
   }

    public function test_issue_badge_failure(){
        global $DB;
        $time = time();
        $url = 'url';
        $token = 'token';

        $this->assert_pending_badge_count(0);
        $pending_badge = $this->create_pending_badge();
        $DB->insert_record('block_acclaim_pending_badges', $pending_badge);
        $this->assert_pending_badge_count(1);

        $mock_curl = $this->assert_curl(401, $url, $token, $time);
        $return_code = block_acclaim_issue_badge(
            $mock_curl, $time, $url, $token);
        $this->assertEquals(401, $return_code);
        $this->assert_pending_badge_count(1);
   }

    public function test_issue_badge_mutltiple(){
        global $DB;
        $time = time();
        $url = 'url';
        $token = 'token';

        $this->assert_pending_badge_count(0);
        $pending_badge = $this->create_pending_badge();
        $DB->insert_record('block_acclaim_pending_badges', $pending_badge);
        $DB->insert_record('block_acclaim_pending_badges', $pending_badge);
        $this->assert_pending_badge_count(2);

        $mock_curl = $this->assert_curl(201, $url, $token, $time, 2);
        $return_code = block_acclaim_issue_badge(
            $mock_curl, $time, $url, $token);
        $this->assertEquals(201, $return_code);

        $this->assert_pending_badge_count(0);
   }

   public function test_get_badges()
   {
        $url = 'url';
        $token = 'token';
        $mock_response = json_encode(array("bogus" => "data"));

        $mock_curl = $this->createMock(curl::class);
        $mock_curl->method("get")
                  ->with(
                    'url',
                    array("sort" => "name", "filter" => "state::active"),
                    array("CURLOPT_USERPWD" => "token:"))
                 ->willReturn($mock_response);

        $response = block_acclaim_return_json_badges($mock_curl, $url, $token);
        $this->assertEquals($mock_response, $response);
   }

   public function test_badgename()
   {
       $fromform = $this->mock_form();
       $fromform = block_acclaim_update_form_with_badge_name($fromform);
       $this->assertEquals("def",$fromform->badgename);
   }
}
