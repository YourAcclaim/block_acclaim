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
 * A form to select which badge this course issues on completion.
 * @see https://docs.moodle.org/dev/Form_API
 *
 * @package    block_acclaim
 * @copyright  2020 Credly, Inc. <http://youracclaim.com>
 * @license    https://opensource.org/licenses/MIT
 */
require_once("{$CFG->libdir}/formslib.php");
require_once($CFG->dirroot . '/blocks/acclaim/lib.php');

class block_acclaim_form extends moodleform {
    function definition() {
        $mform =& $this->_form;

        //populate form
        $mform->addElement('header','displayinfo', get_string('select_badge', 'block_acclaim'));
        $badge_items = (new \block_acclaim_lib())->badge_names();
        $mform->addElement('select', 'badgeid', get_string('acclaim_badges', 'block_acclaim'), $badge_items, '');
        $mform->addElement('date_time_selector', 'expiration', get_string('expires', 'block_acclaim'), array('optional' => true));
        $mform->setAdvanced('optional');

        // hidden elements
        $mform->addElement('hidden', 'blockid');
        $mform->setType('blockid', PARAM_INT);
        $mform->addElement('hidden', 'courseid');
        $mform->setType('courseid', PARAM_INT);
        $mform->addElement('hidden','badgename', json_encode($badge_items));
        $mform->setType('badgename', PARAM_RAW);
        $this->add_action_buttons();
    }
}
