# Test Block
```
    public function get_content() { 
        if (isset($this->content)) { 
            return $this->content; 
        } 

        // $renderable = new block_recentlyaccessedcourses\output\main(); 
        $renderable = 'TEst';        
```
###### Description
This was an attempt to add a card structure to the block using a renderer file and mustache templates. 
##### Parameters
$renderable: The content which needed to be rendered. 
$renderer: The page containing the rendering functions 

&nbsp;
```
$renderer = $this->page->get_renderer('block_testblock'); 
```
##### Description 
Intended to find and retrieve the document with code for rendering within the testblock block. 
##### Parameters 
‘block_testblock’: the rendering function to be retrieved by the function 
##### What this function returns 
This function was expected to return the renderer for block_testblock, however was unable to locate the renderer. 

&nbsp;
```
$this->content = new stdClass(); 
        $this->content->text = $renderer->render($renderable); 
        $this->content->footer = ''; 

        return $this->content; 
```
##### Description
Intended to call the render function using the predefined renderer and render the sample ’TEst’ text. 
##### Parameters 
$this->content->text: The text to be displayed within the content object. 
render($renderable): The final rendered object 
##### What this function returns 
This function was intended to return the sample text after it had been rendered. 
However it returns an error message, ‘request for an unknown renderer block_testblock 

&nbsp;
```
class block_testblock_renderer extends plugin_renderer_base { 

    /** 
     * Return the main content for the Recently accessed courses block. 
     * 
     * @param main $main The main renderable 
     * @return string HTML string 
     */ 
    public function render_testblock($data) { 
        // return $this->render_from_template('block_testblock/main', $main->export_for_template($this)); 

        return $this->content->$data; 
```
##### Description 
Basic renderer code with no changes made to content.  
##### Parameters
block_testblock_renderer: Renderer for the block_testblock 
plugin_renderer_base: The basic renderer class 
$data: Data inputted to be rendered 
##### This function returns 
The aim was to produce a simple unchanged string and then build upon the base code. 
However the renderer did not produce any working output 
