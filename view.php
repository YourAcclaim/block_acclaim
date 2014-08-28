<?php

require_once('../../config.php');
require_once('acclaim_form.php');

global $DB, $OUTPUT, $PAGE;

$courseid = required_param('courseid', PARAM_INT);

if (!$course = $DB->get_record('course', array('id' => $courseid))) {
    print_error('invalidcourse', 'block_acclaim', $courseid);
}

require_login($course);

$PAGE->set_url('/blocks/acclaim/view.php', array('id' => $courseid));
$PAGE->set_pagelayout('standard');
$PAGE->set_heading('edithtml');

$acclaim = new acclaim_form();

if(!empty($blockid)){
    $toform['blockid'] = $blockid;
}

if(!empty($courseid)){
    $toform['courseid'] = $courseid;
}

$acclaim->set_data($toform);

if($acclaim->is_cancelled()) {
    $courseurl = new moodle_url('/course/view.php', array('id' => $id));
    redirect($courseurl);
} else if ($fromform = $acclaim->get_data()) {
    $courseurl = new moodle_url('/course/view.php', array('id' => $courseid));
    print_object($fromform);
    // We need to add code to appropriately act on and store the submitted data
    if (!$DB->insert_record('block_acclaim', $fromform)) {
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
