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
 * @package    block_acclaim
 * @copyright  2020 Credly, Inc. <http://youracclaim.com>
 * @license    https://opensource.org/licenses/MIT
 */

// Re-reading this file requires a version change.
// In development, set this in config.php instead: $CFG->langstringcache = false;
$string['pluginname'] = 'Credly';
$string['acclaim'] = 'Credly';
$string['config_header'] = 'Create a new Credly block for this course';
$string['config_instructions'] = 'This block will issue an Credly badge on course completion. The badge may be selected from the course page.';
$string['acclaim:addinstance'] = 'Add a new Credly block';
$string['acclaim:myaddinstance'] = 'Add a new Credly block to the My Moodle page';
$string['acclaim:editbadge'] = 'Select a badge to associate with a course';
$string['privacy:metadata'] = 'The Credly block only stores course and badge template details. User data is stored in a temporary table and removed once issued';
$string['select_badge'] = 'Select Badge';
$string['acclaim_badges'] = 'Credly Badges';
$string['expires'] = 'Expires';
$string['production'] = 'Production'; // The name of the Acclaim production server.
$string['sandbox'] = 'Sandbox'; // The name of the Acclaim demo server.
$string['setting_domain'] = 'Credly Server';
$string['setting_org_id'] = 'Credly Organization ID';
$string['setting_org_help'] = 'Example: {$a}';
$string['setting_app_token'] = 'Credly App Token';
$string['setting_app_token_help'] = 'This is obtained from Credly support. Example: {$a}';
$string['setting_app_token_help'] = 'This is obtained from Credly support. Example: {$a}';
$string['issuecredentials_task_name'] = 'Credly - Issue Badges';
