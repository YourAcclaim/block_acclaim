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
	global $DB;
	$this->resetAfterTest(true);
    }

    public function mock_event(){
        
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
        $event->userid = "2";
        $event->courseid = "123";
        $event->relateduserid = "2";
        $event->anonymous = "0";
        $event->timecreated = "1409543537";
        return $event;
    }

    public function test_get_badge_id(){
        global $DB;
        $table = 'block_acclaim';
        $DB->delete_records($table);
        $this->assertEmpty($DB->get_records($table));

        $event = $this->mock_event();
        
        $dataobject = new stdClass();
        $dataobject->badgeid = '919309fc-648c-42cb-9415-7f8ecf2f681f';
        $dataobject->courseid = $event->courseid;
        $dataobject->expiration = 0;
        $DB->insert_record($table, $dataobject, $returnid=true, $bulk=false);
        
        $badge_id = get_badge_id($event);
        $this->assertEquals($dataobject->badgeid,$badge_id);
    }

    public function test_something() {
	$test_val = test_the_test();
	$this->assertEquals($test_val,'test');	
    }
}
