<?php

require_once('../../config.php');
require_once('testblock_form.php');

global $DB, $OUTPUT, $PAGE;

// Check for all required variables.
$courseid = required_param('courseid', PARAM_INT);

$blockid = required_param('blockid', PARAM_INT);

// Next look for optional variables.
$id = optional_param('id', 0, PARAM_INT);

if (!$course = $DB->get_record('course', array('id' => $courseid))) {
    print_error('invalidcourse', 'block_testblock', $courseid);
}

require_login($course);
$PAGE->set_url('/blocks/testblock/view.php', array('id' => $courseid));
$PAGE->set_pagelayout('standard');
$PAGE->set_heading(get_string('edithtml', 'block_testblock'));

$testblock = new testblock_form();
$toform['blockid'] = $blockid;
$toform['courseid'] = $courseid;
$testblock->set_data($toform);

$settingsnode = $PAGE->settingsnav->add(get_string('testblocksettings', 'block_testblock'));
$editurl = new moodle_url('/blocks/testblock/view.php', array('id' => $id, 'courseid' => $courseid, 'blockid' => $blockid));
$editnode = $settingsnode->add(get_string('editpage', 'block_testblock'), $editurl);
$editnode->make_active();

if($testblock->is_cancelled()) {
    // Cancelled forms redirect to the course main page.
    $courseurl = new moodle_url('/course/view.php', array('id' => $id));
    redirect($courseurl);
// } else if ($testblock->get_data()) {
} else if ($fromform = $testblock->get_data()) {
    // We need to add code to appropriately act on and store the submitted data
    if (!$DB->insert_record('block_testblock', $fromform)) {
        print_error('inserterror', 'block_testblock');
    }
    // but for now we will just redirect back to the course main page.
    $courseurl = new moodle_url('/blocks/testblock/some_file.php', array('id' => $courseid));
    // redirect($courseurl);
    print_object($fromform);
} else {
    // form didn't validate or this is the first display
    $site = get_site();
    echo $OUTPUT->header();
    $testblock->display();
    echo $OUTPUT->footer();
}

// echo $OUTPUT->header();
// $testblock->display();
// echo $OUTPUT->footer();

$testblock->display();
?>