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
 * Class definition for the Recently accessed courses block.
 *
 * @package    block_testblock
 * @copyright  2018 Victor Deniz <victor@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

class block_testblock extends block_list {

    public function init() {
        $this->title = get_string('pluginname', 'block_testblock');
    }
    
    /**
     * Allow the block to have configuration page.
     *
     * @return boolean 
     */
    function has_config() {
        return true;
    }
    /**
     * Returns the contents.
     *
     * @return stdClass contents of block
     */
  

    public function get_content(){
        if ($this->content !== null) {
            return $this->content;
        }
        
        global $DB;

        $this->content = new stdClass;
        $this->content->items[] = html_writer::tag('a', 'Menu Option 1', array('href' => '/blocks/testblock/some_file.php'));
        // $this->content->icons[] = html_writer::empty_tag('img', array('src' => 'images/icons/1.gif', 'class' => 'icon'));
        // $this->content->items[] = 'Hello';
        // echo 'Hello';
        
        //Returns current categories in course database, these will be used to match recommendations
        $courses = get_courses();
        foreach ($courses as $id=>$course) {
            $category = $DB->get_record('course_categories',array('id'=>$course->category));
            $course->categoryName = $category->name;
            $allcourses[$id] = $course;
        }
        //Add search element using categories in all courses block
        //Return the recommendations and display them

        //The following code will depend on how the recommendation search returns data 
        foreach ($allcourses as $id=>$course) {
            // $this->content->items[] = $course->categoryName;
            $this->content->items[] = html_writer::tag('a', $course->categoryName, array('href' => '/blocks/testblock/some_file.php'));

        }
        
        
        $this->content->footer = 'Footer';

        return $this->content;
    }
    
    // public function get_content() {
    //     if (isset($this->content)) {
    //         return $this->content;
    //     }

    //     $renderable = new block_testblock\output\main();
    //     $renderer = $this->page->get_renderer('block_testblock');

    //     $this->content = new stdClass();
    //     $this->content->text = $renderer->render($renderable);
    //     $this->content->footer = '';

    //     return $this->content;
    // }

    // /**
    //  * Return the plugin config settings for external functions.
    //  *
    //  * @return stdClass the configs for both the block instance and plugin
    //  * @since Moodle 3.8
    //  */
    // public function get_config_for_external() {
    //     // Return all settings for all users since it is safe (no private keys, etc..).
    //     $configs = get_config('block_testblock');

    //     return (object) [
    //         'instance' => new stdClass(),
    //         'plugin' => $configs,
    //     ];
    // }
}