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
 * Main entrypoint for the acclaim block.
 *
 * @package    block_acclaim
 * @copyright  2020 Credly, Inc. <http://youracclaim.com>
 * @license    https://opensource.org/licenses/MIT
 */
defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/blocks/acclaim/lib.php');

class block_acclaim extends block_base {
    private $acclaim = null;

    /**
     * Initialize the block.
     *
     * @return string
     */
    public function init() {
        $this->acclaim = new \block_acclaim_lib();
        $this->title = get_string('acclaim', 'block_acclaim');
    }

    /**
     * This block can be configured.
     *
     * @return boolean
     */
    public function has_config() {
        return true;
    }

    /**
     * Display specialized text for the course block (the selected badge name).
     */
    public function specialization() {
        if (isset($this->config)) {
            global $COURSE;
            $course_id = $COURSE->id;

            $badge_name = $this->acclaim->get_course_info($course_id, 'badgename');

            if ($badge_name == '') {
                $badge_name = 'No Badge Selected';
            }

            $this->config->text = $badge_name;
        }
    }

    /**
     * Allow multiple blocks per course, so that a course can issue multiple badges.
     *
     * @return boolean
     */
    function instance_allow_multiple() {
        return true;
    }

    /**
     * This plugin is only valid in the course view.
     *
     * @return string
     */
    public function applicable_formats() {
        return array(
            'site-index' => false,
            'course-view' => true,
            'course-view-social' => false,
            'mod' => false,
            'mod-quiz' => false
        );
    }

    /**
     * Generate UI from view.php.
     *
     * @return string
     */
    public function get_content() {
        global $COURSE, $DB, $OUTPUT, $CFG, $PAGE;

        if ($this->content !== null) {
            return $this->content;
        }

        $this->content = new stdClass;
        if (!empty($this->config->text)) {
            $this->content->text = $this->config->text;
        }
        
        $url = new moodle_url(
            '/blocks/acclaim/view.php',
            array('blockid' => $this->instance->id, 'courseid' => $COURSE->id)
        );
    
        $context = context_course::instance($COURSE->id);
        if (has_capability('block/acclaim:editbadge', $this->context)) {
            $this->content->footer = html_writer::link($url, get_string('select_badge', 'block_acclaim'));
        }
        return $this->content;
    }
}
