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
 * Group observers.
 *
 * @package    block_acclaim
 * @copyright  2014 Yancy Ribbens <yancy.ribbens@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace block_acclaim;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/blocks/acclaim/lib.php');

class group_observers {
    public static function issue_badge($event) {
	global $DB;
        
        $course = get_block_course($event->courseid);
        $expires_timestamp = "";
                
        if($course->expiration){
            $expires_timestamp = $course->expiration;
        }

        $data = create_data_array($event,$course->badgeid,$expires_timestamp);
        $url = get_issue_badge_url();
        $token = get_request_token();
        $return_code = issue_badge_request($data,$url,$token);
        if($return_code != 201){
            error_log("failed to issue badge, return code: ".$return_code);
            }
    }
}

?>



