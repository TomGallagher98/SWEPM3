<?php

class block_testblock_edit_form extends block_edit_form {
        
    protected function specific_definition($mform) {
        
        // Section header title according to language file.
        $mform->addElement('header', 'config_header', get_string('blocksettings', 'block'));

        // A sample string variable with a default value.
        $mform->addElement('text', 'config_text', get_string('blockstring', 'block_testblock'));
        $mform->setDefault('config_text', 'default value');
        $mform->setType('config_text', PARAM_RAW);

        if (! empty($this->config->text)) {
            $this->content->text = $this->config->text;
        }

        $mform->addElement('text', 'config_title', get_string('testblock', 'block_testblock'));
        $mform->setDefault('config_title', 'default value');
        $mform->setType('config_title', PARAM_TEXT);

    }
}