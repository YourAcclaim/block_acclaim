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
 * course_overview block settings
 *
 * @package    block_course_overview
 * @copyright  2012 Adam Olley <adam.olley@netspot.com.au>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die;

if ($ADMIN->fulltree) {
    $settings->add(new admin_setting_configtext( 'block_acclaim/url', 'Acclaim URL', 'Example: https://youracclaim.com', null, PARAM_TEXT));
    $settings->add(new admin_setting_configtext( 'block_acclaim/org', 'Organization ID', 'Example: 6bb2e1c7-c66b-4d47-9301-4a6b9e792e2c', null, PARAM_TEXT));
    $settings->add(new admin_setting_configtext( 'block_acclaim/token', 'Token', 'Example: FZ9QZ4sDtEwNR7Tcv-Yi', null, PARAM_TEXT));
}
