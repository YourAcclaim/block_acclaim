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
 * @copyright  2014 Yancy Ribbens
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
GLOBAL $CFG;

defined('MOODLE_INTERNAL') || die();

require_once(dirname(__FILE__) . '/../../../config.php');
require_once($CFG->dirroot . '/blocks/acclaim/lib.php');

class acclaim_lib_test extends advanced_testcase{
    function setUp(){
//	global $DB;
	$this->resetAfterTest(true);
    }

    public function mock_event($id){
        
        //[eventname] => \core\event\course_completed
        //[component] => core
        //[action] => completed
        //[target] => course
        //[objecttable] => course_completions
        //[objectid] => 23
        //[crud] => u
        //[edulevel] => 2
        //[contextid] => 147
        //[contextlevel] => 50
        //[contextinstanceid] => 14
        //[userid] => 2
        //[courseid] => 14
        //[relateduserid] => 2
        //[anonymous] => 0
        //[other] => Array
            //(
                //[relateduserid] => 2
            //)
        //[timecreated] => 1409543537
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
        $event->userid = $id;
        $event->courseid = "123";
        $event->relateduserid = "2";
        $event->anonymous = "0";
        $event->timecreated = "1409543537";
        return $event;
    }

    private function mock_form()
    {
        //[badgeid] => 1edb816d-a9fb-445d-b024-bb52075718e5
        //[expiration] => 0
        //[blockid] => 
        //[courseid] => 38
        //[submitbutton] => Save changes

        $fromform = new stdClass();
        $fromform->badgeid = "1edb816d-a9fb-445d-b024-bb52075718e5";
        $fromform->expiration = 0;
        $fromform->blockid = "";
        $fromform->courseid = "38";
        $fromform->submitbutton = "Save changes";

        return $fromform;
    }


    public function test_get_badge_id(){
        global $DB;
        $table = 'block_acclaim';
        $DB->delete_records($table);
        $this->assertEmpty($DB->get_records($table));

        $event = $this->mock_event('2');
        
        $dataobject = new stdClass();
        $dataobject->badgeid = '919309fc-648c-42cb-9415-7f8ecf2f681f';
        $dataobject->courseid = $event->courseid;
        $dataobject->expiration = 0;
        $DB->insert_record($table, $dataobject, $returnid=true, $bulk=false);
        
        $badge_id = get_badge_id($event->courseid);
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
        $DB->insert_record($table, $dataobject, $returnid=true, $bulk=false);

        $course = get_block_course($event->courseid);
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

       $result = write_badge_to_issue($fromform);
       $count = $DB->count_records_select($table, "courseid = '38'");
       
       $this->assertEquals($count,1);

       $result = write_badge_to_issue($fromform);
       $count = $DB->count_records_select($table, "courseid = '38'");

       $this->assertEquals($count,1);
   }


   public function test_create_array()
   {
        $badge_id = "123";
        $data = create_data_array($this->mock_event('2'),$badge_id,"",'1');
        $is_set = isset($data);
        $this->assertEquals(true,$is_set);
   }

   public function test_issue_badge()
   {
       global $DB;
       $user = $this->getDataGenerator()->create_user(array('email'=>'user1@example.com', 'username'=>'user1'));
       //$user = $this->getDataGenerator()->create_user();
       $id = $user->id;
       $badge_id = '919309fc-648c-42cb-9415-7f8ecf2f681f';
       $expires = '';
       $data = create_data_array($this->mock_event($id),$badge_id,"",$id);

       $target_url = "https://jefferson-staging.herokuapp.com/api/v1/organizations/6bb2e1c7-c66b-4d47-9301-4a6b9e792e2c/badges";

       $token = getenv('token');
       $return_code = issue_badge_request($data,$target_url,$token);
       $this->assertEquals(201,$return_code);
   }

//cant get this test to work because it is unable to create config_plugin table
//circle back
//    public function test_get_issue_badge_url(){
//        global $DB;
//        $this->resetAfterTest(false);
 //       $DB->delete_records($table);
//        $this->assertEmpty($DB->get_records($table));
//        $dataobject = new stdClass();
//        $dataobject->plugin = "block_acclaim";
//        $dataobject->name = "url";
//        $dataobject->value = "https://jefferson-staging.herokuapp.com";
        
//        $dataobject2 = new stdClass();
//        $dataobject2->plugin = "block_acclaim";
//        $dataobject2->name = "org";
//        $dataobject2->value = "6bb2e1c7-c66b-4d47-9301-4a6b9e792e2c";

//        $objects = array($dataobject,$dataobject2);

//        $DB->insert_record($table, $objects);

//        $target_url = "https://jefferson-staging.herokuapp.com/api/v1/organizations/6bb2e1c7-c66b-4d47-9301-4a6b9e792e2c/badges";
  //      $this->assertEquals($target_url,get_issue_badge_url());
//    }
}
