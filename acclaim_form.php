<?php

require_once("{$CFG->libdir}/formslib.php");
require_once($CFG->dirroot.'/blocks/acclaim/lib.php');

class acclaim_form extends moodleform {

    function definition(){
        $mform =& $this->_form;
    
	// add group for text areas
	$mform->addElement('header','displayinfo', 'Acclaim Badges');
 
	// add image selector radio buttons
	$badge_items = block_acclaim_images();
	//$radioarray = array();

	//for ($i = 0; $i < count($images); $i++) {
    	//    $radioarray[] =& $mform->createElement('radio', 'picture', '', $images[$i], $i);
	//}

	//$mform->addGroup($radioarray, 'radioar', 'Badge Select', array(' '), FALSE);
        
        //$FORUM_TYPES = array();
        //$FORUM_TYPES["foo"] = "bar";
        //$FORUM_TYPES["bar"] = "foo";

        $mform->addElement('select', 'type', 'forum', $badge_items, '');
        	
	// add optional grouping
	$mform->addElement('header', 'optional', 'optional', null, false); // add date_time selector in optional area
	$mform->addElement('date_time_selector', 'displaydate', 'Expires', array('optional' => true));
	$mform->setAdvanced('optional');

        // hidden elements
        $mform->addElement('hidden', 'blockid');
        $mform->addElement('hidden', 'courseid');
	
	$this->add_action_buttons();
	}
}
