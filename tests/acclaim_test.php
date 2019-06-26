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

       $this->assertEquals($count,1);
   }

   public function test_create_array()
   {
        $badge_id = "123";
        $data = block_acclaim_create_data_array($this->mock_event('2'),$badge_id,"",'1');
        $is_set = isset($data);
        $this->assertEquals(true,$is_set);
   }

   public function test_issue_badge()
   {
      global $DB;
      $word = array_merge(range('a', 'z'), range('A', 'Z'));
      shuffle($word);
      $rand_word = substr(implode($word), 0, 10);
      $user = $this->getDataGenerator()->create_user(array('email'=>$rand_word."@moodle.com"));
      $id = $user->id;
        
      $event = $this->mock_event($id);
      $badge_id = 'fc509375-3a4b-4b9e-b0b3-3e4ad784e545';
       
      $data = block_acclaim_create_data_array($event,$badge_id,'');

      $target_url = "https://jefferson-staging.herokuapp.com/api/v1/organizations//a568b3cd-72a3-4312-9423-bb23b13d9d6a/badges";

      $token = getenv('token');
      $return_code = block_acclaim_issue_badge_request($data,$target_url,$token);
      $this->assertEquals(201,$return_code);
   }

   public function test_badgename()
   {
       $fromform = $this->mock_form();
       $fromform = block_acclaim_update_form_with_badge_name($fromform);
       $this->assertEquals("def",$fromform->badgename);
   }
}
