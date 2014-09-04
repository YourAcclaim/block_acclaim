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
*
* @package    block_acclaim
* @copyright  2014 Yancy Ribbens <yancy.ribbens@gmail.com>
* @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
*/
require_once("{$CFG->libdir}/formslib.php");
require_once($CFG->dirroot.'/blocks/acclaim/lib.php');

class acclaim_form extends moodleform {

    function definition(){
        $mform =& $this->_form;
   
	//populate form 
	$mform->addElement('header','displayinfo', 'Select Badge');
	$badge_items = block_acclaim_images();
        $mform->addElement('select', 'badgeid', 'Acclaim Badges', $badge_items, '');
	//$mform->addElement('header', 'optional', 'Optional Settings', null, false); // add date_time selector in optional area
	$mform->addElement('date_time_selector', 'expiration', 'Expires', array('optional' => true));
	$mform->setAdvanced('optional');

        // hidden elements
        $mform->addElement('hidden', 'blockid');
        $mform->addElement('hidden', 'courseid');
        $mform->addElement('hidden','badgename',json_encode($badge_items));
        	
	$this->add_action_buttons();
	}
}
