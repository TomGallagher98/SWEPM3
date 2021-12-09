<?php

require_once("{$CFG->libdir}/formslib.php");

class testblock_form extends moodleform {
    
    function definition() {
        
        $mform =& $this->_form;
        $mform->addElement('header','displayinfo', get_string('textfields', 'block_testblock'));
        
        // add page title element.
        $mform->addElement('text', 'pagetitle', get_string('edithtml', 'block_testblock'));
        $mform->setType('pagetitle', PARAM_RAW);
        $mform->addRule('pagetitle', null, 'required', null, 'client');
                
        // add display text field
        $mform->addElement('htmleditor', 'displaytext', get_string('displayedhtml', 'block_testblock'));
        $mform->setType('displaytext', PARAM_RAW);
        $mform->addRule('displaytext', null, 'required', null, 'client');

        $mform->addElement('hidden', 'blockid');
        $mform->addElement('hidden','courseid');
        $this->add_action_buttons();
    }

   
}