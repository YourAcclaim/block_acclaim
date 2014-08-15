<?php

require_once("{$CFG->libdir}/formslib.php");
require_once($CFG->dirroot.'/blocks/acclaim/lib.php');

class acclaim_form extends moodleform {

    function definition(){
        $mform =& $this->_form;
    
	// add group for text areas
	$mform->addElement('header','displayinfo', 'Acclaim Badges');
 
	// add image selector radio buttons
	$images = block_acclaim_images();
	$radioarray = array();
	for ($i = 0; $i < count($images); $i++) {
    	    $radioarray[] =& $mform->createElement('radio', 'picture', '', $images[$i], $i);
	}

	$mform->addGroup($radioarray, 'radioar', 'Badge Select', array(' '), FALSE);
	}
}
