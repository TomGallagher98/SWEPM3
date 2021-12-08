<?php
// class block_testblock extends block_base {
class block_testblock extends block_list {
    public function init() {
        $this->title = get_string('pluginname', 'block_testblock');
    }

    function has_config() {return true;}

    // public function get_content() {

    //     global $USER, $DB, $COURSE, $OUTPUT;

    //     if ($this->content !== null) {
    //         return $this->content;
    //     }
    //     $this->content = new stdClass;
    //     $this->content->text = 'A simple test block';
    //     // $this->content->footer = 'Test block Footer';
    //     // $url = new moodle_url('/blocks/testblock/view.php', array('blockid' => $this->instance->id, 'courseid' => $COURSE->id));
    //     $url = new moodle_url('/blocks/testblock/view.php', array('blockid' => $this->instance->id, 'courseid' => $COURSE->id));
    //     // $this->content->footer = html_writer::link($url, get_string('addpage', 'block_testblock'));
    //     $this->content->footer = html_writer::link($url, get_string('addpage', 'block_testblock'));


    //     return $this->content;
    // }
    // List configuration
    public function get_content(){
        if ($this->content !== null) {
            return $this->content;
        }

        $this->content = new stdClass;
        $this->content->items[] = html_writer::tag('a', 'Menu Option 1', array('href' => '/blocks/testblock/some_file.php'));
        $this->content->icons[] = html_writer::empty_tag('img', array('src' => 'images/icons/1.gif', 'class' => 'icon'));
        $this->content->footer = 'Footer';

        return $this->content;
    }

    public function specialization() {
        if (isset($this->config)) {
            if (empty($this->config->title)) {
                $this->title = get_string('defaulttitle', 'block_testblock');
            } else {
                $this->title = $this->config->title;
            }

            if (empty($this->config->text)) {
                $this->congif->text = get_string('defaulttext', 'block_testblock');
            }
        }
    }

    public function instance_config_save($data, $nolongerused = false){
        if(get_config('testblock', 'Allow_HTML') == 1){
            $data->text = strip_tags($data->text);
        }
        return parent::instance_config_save($data, $nolongerused);
    }
}
