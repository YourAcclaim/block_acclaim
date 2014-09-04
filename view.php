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
 * @package    block_acclaim
 * @copyright  2014 Yancy Ribbens <yancy.ribbens@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
*/

require_once('../../config.php');
require_once('acclaim_form.php');
require_once($CFG->dirroot . '/blocks/acclaim/lib.php');

global $DB, $OUTPUT, $PAGE;

$courseid = required_param('courseid', PARAM_INT);
$blockid = required_param('blockid', PARAM_INT);
$id = optional_param('id', 0, PARAM_INT);

error_log("courseid: ".$courseid);

if (!$course = $DB->get_record('course', array('id' => $courseid))) {
    print_error('invalidcourse', 'block_acclaim', $courseid);
}

require_login($course);

$PAGE->set_url('/blocks/acclaim/view.php', array('id' => $courseid));
$PAGE->set_pagelayout('standard');
$PAGE->set_heading('Acclaim');

$settingsnode = $PAGE->settingsnav->add('acclaim');
$editurl = new moodle_url('/blocks/acclaim/view.php', array('id' => $id, 'courseid' => $courseid, 'blockid' => $blockid));
$editnode = $settingsnode->add('Select Badge', $editurl);
$editnode->make_active();

$acclaim = new acclaim_form();

$toform['blockid'] = $blockid;
$toform['courseid'] = $courseid;

$acclaim->set_data($toform);

if($acclaim->is_cancelled()) {
    $courseurl = new moodle_url('/course/view.php', array('id' => $courseid));
    redirect($courseurl);
} else if ($fromform = $acclaim->get_data()) {
    $courseurl = new moodle_url('/course/view.php', array('id' => $courseid));
    //print_object($fromform);
    // We need to add code to appropriately act on and store the submitted data
    if (!write_badge_to_issue($fromform)) {
        print_error('inserterror', 'block_acclaim');
    }
    redirect($courseurl);
} else {
    // form didn't validate or this is the first display
    $site = get_site();
    echo $OUTPUT->header();
    $acclaim->display();
    echo $OUTPUT->footer();
}

?>
