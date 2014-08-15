<?php
 
class block_acclaim_edit_form extends block_edit_form {
 
    protected function specific_definition($mform) {
	error_log("specific_definition"); 
        // Section header title according to language file.
        $mform->addElement('header', 'configheader', get_string('blocksettings', 'block'));
 
        // A sample string variable with a default value.
        $mform->addElement('text', 'config_text', get_string('blockstring', 'block_acclaim'));
        $mform->setDefault('config_text', 'default value');
        $mform->setType('config_text', PARAM_RAW);        

    }
}
