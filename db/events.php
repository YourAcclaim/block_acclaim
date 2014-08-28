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
 * Add event handlers for the quiz
 *
 * @package    mod_acclaim
 * @category   event
 * @copyright  2014 Yancy Ribbens
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

//$handlers = array(
//    'user_enrolled' => array (
//	'handlerfile'      => '/mod/acclaim/lib.php',
//        'handlerfunction'  => 'issue_badge',
//        'schedule'         => 'instant',
//        'internal'         => 1,
//    ),
//);

$observers = array(
    array(
        'eventname' => '\core\event\course_completed',
        'callback' => '\block_acclaim\group_observers::issue_badge',
    ),
);
