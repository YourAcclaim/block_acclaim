<?php

require_once("{$CFG->libdir}/formslib.php");
require_once($CFG->dirroot.'/blocks/acclaim/lib.php');

class acclaim_form extends moodleform {

    function definition(){
        $mform =& $this->_form;
   
	//populate form 
	$mform->addElement('header','displayinfo', 'Acclaim Badges');
	$badge_items = block_acclaim_images();
        $mform->addElement('select', 'type', 'forum', $badge_items, '');
	$mform->addElement('header', 'optional', 'optional', null, false); // add date_time selector in optional area
	$mform->addElement('date_time_selector', 'displaydate', 'Expires', array('optional' => true));
	$mform->setAdvanced('optional');

        // hidden elements
        $mform->addElement('hidden', 'blockid');
        $mform->addElement('hidden', 'courseid');
	
	$this->add_action_buttons();
	}
}
