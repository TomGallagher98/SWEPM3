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

class block_testblock extends block_base {

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
  

    
    
    public function get_content() {
        if (isset($this->content)) {
            return $this->content;
        }

        // $renderable = new block_recentlyaccessedcourses\output\main();
        $renderable = 'TEst';
        $renderer = $this->page->get_renderer('block_testblock');

        $this->content = new stdClass();
        $this->content->text = $renderer->render($renderable);
        $this->content->footer = '';

        return $this->content;
    
    }

    
}