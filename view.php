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

//echo $OUTPUT->header();
//$acclaim->display();
//echo $OUTPUT->footer();

if($acclaim->is_cancelled()) {
    // Cancelled forms redirect to the course main page.
    $courseurl = new moodle_url('/course/view.php', array('id' => $id));
    redirect($courseurl);
} else if ($acclaim->get_data()) {
    // We need to add code to appropriately act on and store the submitted data
    // but for now we will just redirect back to the course main page.
    $courseurl = new moodle_url('/course/view.php', array('id' => $courseid));
    redirect($courseurl);
} else {
    // form didn't validate or this is the first display
    $site = get_site();
    echo $OUTPUT->header();
    $acclaim->display();
    echo $OUTPUT->footer();
}



?>


