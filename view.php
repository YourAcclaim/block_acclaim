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
 * course_overview block settings.
 *
 * @package    block_acclaim
 * @copyright  2020 Credly, Inc. <http://youracclaim.com>
 * @license    https://opensource.org/licenses/MIT
 */
require_once('../../config.php');
require_once('./block_acclaim_form.php');
require_once($CFG->dirroot . '/blocks/acclaim/lib.php');

global $DB, $OUTPUT, $PAGE;

$courseid = required_param('courseid', PARAM_INT);
$blockid = required_param('blockid', PARAM_INT);
$id = optional_param('id', 0, PARAM_INT);

if (!$course = $DB->get_record('course', array('id' => $courseid))) {
    print_error('invalidcourse', 'block_acclaim', $courseid);
}

require_login($course);

$PAGE->set_url('/blocks/acclaim/view.php', array('id' => $courseid));
$PAGE->set_pagelayout('standard');
$PAGE->set_heading(get_string('acclaim', 'block_acclaim'));

$settingsnode = $PAGE->settingsnav->add('Credly');
$editurl = new moodle_url('/blocks/acclaim/view.php', array('id' => $id, 'courseid' => $courseid, 'blockid' => $blockid));
$editnode = $settingsnode->add(get_string('select_badge', 'block_acclaim'), $editurl);
$editnode->make_active();

$acclaim_form_data = new block_acclaim_form();

$toform['blockid'] = $blockid;
$toform['courseid'] = $courseid;
$block_acclaim_course = $DB->get_record('block_acclaim_courses', array('courseid' => $courseid));
if ($block_acclaim_course) {
    $toform['badgeid'] = $block_acclaim_course->badgeid;
    $toform['expiration'] = $block_acclaim_course->expiration;
}

$acclaim_form_data->set_data($toform);

if ($acclaim_form_data->is_cancelled()) {
    $courseurl = new moodle_url('/course/view.php', array('id' => $courseid));
    redirect($courseurl);
} else if ($fromform = $acclaim_form_data->get_data()) {
    $acclaim = new \block_acclaim_lib();
    $courseurl = new moodle_url('/course/view.php', array('id' => $courseid));
    // We need to add code to appropriately act on and store the submitted data
    if (!$acclaim->set_course_badge_template($fromform)) {
        print_error('inserterror', 'block_acclaim');
    }
    redirect($courseurl);
} else {
    // form didn't validate or this is the first display
    $site = get_site();
    echo $OUTPUT->header();
    $acclaim_form_data->display();
    echo $OUTPUT->footer();
}
