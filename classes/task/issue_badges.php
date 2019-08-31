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
 * Create request to issue a new credential
 *
 * @package    block_acclaim
 * @copyright  2014 Yancy Ribbens <yancy.ribbens@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_acclaim\task;

defined('MOODLE_INTERNAL') || die();

class issue_badges extends \core\task\scheduled_task {
    public function get_name() {
        return get_string('issuecredentials', 'block_acclaim');
    }

    public function execute() {
        global $CFG;
        require_once($CFG->libdir. '/filelib.php');
        require_once($CFG->dirroot . '/blocks/acclaim/lib.php');
        $url = block_acclaim_get_issue_badge_url();
        $token = block_acclaim_get_request_token();
        $curl = new \curl;
        block_acclaim_issue_badge($curl, time(), $url, $token);
    }
}
