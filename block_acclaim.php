<?php

//require_once('../config.php');
//require_once('acclaim_form.php');

class block_acclaim extends block_base{
    public function init(){
        $this->title = get_string('acclaim', 'block_acclaim');
    }

    public function has_config() {
        return true;
    }

    public function specialization() {
	if (!empty($this->config->title)) {
	    $this->title = $this->config->title;
	} else {
	    $this->config->title = 'Default title ...';
	}

	if (empty($this->config->text)) {
	    $this->config->text = 'Default text ...';
	    }    
    }

    function instance_allow_multiple() {
        return true;
    }

    public function applicable_formats() {
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
        $this->content->text   = 'The content of our Acclaim block!';

	$url = new moodle_url('/blocks/acclaim/view.php', array('blockid' => $this->instance->id, 'courseid' => $COURSE->id));
	error_log('instance id: '.$this->instance->id);
	error_log('course id: '.$COURSE->id);
	error_log('url: '.$url);

        error_log('Context acclaim info array: '.print_r(get_context_info_array($PAGE->context->id),true));

	$url = new moodle_url('/blocks/acclaim/view.php', array('blockid' => $this->instance->id, 'courseid' => $COURSE->id));
	$this->content->footer = html_writer::link($url,'hello');
        
	return $this->content;
    }
            
}
