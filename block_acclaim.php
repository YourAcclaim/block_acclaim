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

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/blocks/acclaim/lib.php');

class block_acclaim extends block_base{
    public function init()
    {
        $this->title = get_string('acclaim', 'block_acclaim');
    }

    public function has_config()
    {
        return true;
    }

    public function specialization()
    {
        global $COURSE;
        $course_id = $COURSE->id;

        $badge_name = block_acclaim_get_badge_info($course_id,"badgename");
        
        if($badge_name == ""){
            $badge_name = "No Badge Selected";
        }
        $this->config = new stdClass;

        $this->config->text = $badge_name;
    }

    function instance_allow_multiple()
    {
        return true;
    }

    public function applicable_formats()
    {
	    return array(
           'site-index' => false,
           'course-view' => true,
           'course-view-social' => false,
           'mod' => false, 
           'mod-quiz' => false
  	    );
    }

    public function get_content(){
        global $COURSE, $DB, $OUTPUT, $CFG, $PAGE;

        if ($this->content !== null) {
            return $this->content;
        }

        $this->content         =  new stdClass;
	    if (! empty($this->config->text)) {
    	    $this->content->text = $this->config->text;
        }
        
        $url = new moodle_url(
            '/blocks/acclaim/view.php',
            array('blockid' => $this->instance->id, 'courseid' => $COURSE->id)
        );
    
        $context = context_course::instance($COURSE->id);
        if(has_capability('block/acclaim:editbadge', $this->context)){
            $this->content->footer = html_writer::link($url,'Select Badge');
        }
        return $this->content;
    }
}
