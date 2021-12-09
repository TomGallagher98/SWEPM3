# Custom activity module – „Learning activity” implementation 
access.php  
```
$capabilities = array( 
    'mod/learningactivity:view' => array( 
        'captype'      => 'read', 
        'contextlevel' => CONTEXT_MODULE, 
        'archetypes'   => array( 
            "student" => CAP_ALLOW, 
            "teacher" => CAP_ALLOW, 
            "editingteacher" => CAP_ALLOW 
        ) 
    ) 
) 
```
##### Description
Creates an array where the capabilities of each role for each event are defined. A contextlevel is given in order to apply the capabilities to a certain level. The captype defines if the capabilities should apply to reading the activity module e.g. viewing it or writing to the activity module e.g. editing it. 

&nbsp;
Index.php 
```
require_once('../../config.php'); 

$id = required_param('id', PARAM_INT); // Course ID 

// Ensure that the course specified is valid 
if (!$course = $DB->get_record('course', array('id'=> $id))) { 
    print_error('Course ID is incorrect'); 
} 
```
##### Description
Gets the id of the course where the activity module is located at. By looking into the moodle DB it can make sure that the course where the activity module is located actually exists. 

&nbsp;
```
require_once('../../config.php'); 
```
##### Description
Make sure that config.php is included at least once 

&nbsp;
```
$id = required_param('id', PARAM_INT); // Course ID 
```
##### Description
Gets the id of the course where the activity module is located at. 

&nbsp;
```
$DB->get_record('course', array('id'=> $id))) 
```
##### Description 
Gets a record from the database where the id of the activity module is listed in the course 

&nbsp;
version.php 
```
defined('MOODLE_INTERNAL') || die(); 

$plugin->version = 2021120800; 
$plugin->requires = 2020110903; 
$plugin->component = 'mod_learningactivity'; 
$plugin->release = "v3.10-r1" 

$plugin->dependencies = [ 
]; 
```
##### Description 
Defines what version of the activity module it is, what version of moodle is required for it to run, how the component is called, and what release it is by defining moodle internal variables intended for use with plugins. Dependencies are also defined in the same way. 

&nbsp;
```
defined('MOODLE_INTERNAL') || die(); 
```
##### Description 
Makes sure that the internal variables of moodle are defined, otherwise don’t execute the code. 

