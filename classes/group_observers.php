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
 * @package    mod_acclaim
 * @copyright  2014 Yancy Ribbens
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace block_acclaim;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/blocks/acclaim/lib.php');

class group_observers {

    public static function issue_badge($event) {
	global $DB;
        $badge_id = get_badge_id($event);
        $data = create_data_array($event,$badge_id,"");
        $url = get_issue_badge_url();
        $token = get_request_token();
        $return_code = issue_badge_request($data,$url,$token);
    }
}

?>



