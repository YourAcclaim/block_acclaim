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
 * Scheduled task to issue badges.
 *
 * @package    block_acclaim
 * @copyright  2020 Credly, Inc. <http://youracclaim.com>
 * @license    https://opensource.org/licenses/MIT
 */
namespace block_acclaim\task;

defined('MOODLE_INTERNAL') || die();
require_once($CFG->dirroot . '/blocks/acclaim/lib.php');

class issue_badges extends \core\task\scheduled_task {
    /**
     * Get the name of the task.
     *
     * @return string
     */
    public function get_name() {
        return get_string('issuecredentials', 'block_acclaim');
    }

    /**
     * Issue a badge (typically due to a course completion event).
     *
     * @return string
     */
    public function execute() {
        global $CFG;
        require_once($CFG->libdir. '/filelib.php');
        require_once($CFG->dirroot . '/blocks/acclaim/lib.php');
        (new block_acclaim_lib())->issue_badge(new \curl);
    }
}
