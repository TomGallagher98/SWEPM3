<?php
class block_recommended_learnings extends block_list {
    public function init() {
        $this->title = get_string('pluginname', 'block_recommended_learnings');
    }

    // public function get_content() {

    //     global $USER, $DB, $COURSE, $OUTPUT;

    //     if ($this->content !== null) {
    //         return $this->content;
    //     }
    //     $this->content = new stdClass;
    //     $this->content->text = 'Recommended Learning Block';
    //     $this->content->footer = 'Recommended Learning Footer';

    //     return $this->content;
    // }

    public function get_content(){
        if ($this->content !== null) {
            return $this->content;
        }
        
        global $DB;

        $this->content = new stdClass;
        $this->content->items[] = get_string('pluginname', 'block_recommended_learnings');
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

    public function specialization() {
        if (isset($this->config)) {
            if (empty($this->config->title)) {
                $this->title = get_string('defaulttitle', 'block_recommended_learnings');
            } else {
                $this->title = $this->config->title;
            }

            if (empty($this->config->text)) {
                $this->congif->text = get_string('defaulttext', 'block_recommended_learnings');
            }
        }
    }
}
