# Moodle Integration
Moodle is a high customizable LMS (learning management system). 
Our goal is to develop a search and block plugin for Moodle, that would:
* push learning activity metadata to an endpoint (search plugin)
* provide custom search in moodle (search plugin)
* provide recommended learning activities view in moodle (block plugin)

These services have previously been used for a proprietary system – we want to show that these are system agnostic and can be used by other e-learning systems. 

## Prerequisites
* [Docker](https://www.docker.com/)
* [Docker Compose v2](https://docs.docker.com/compose/cli-command/)
## Service Setup
In order to get all necessary services running, use the provided docker-compose files:

* docker-compose-persistence.yml
* docker-compose-api.yml

The **persistence** composition file includes containers for a postgres instance (database) and an optional
admin UI for the database (admin) as well as an elasticsearch instance (elastic). The used images are publicly
available at hub.docker.com, and should be downloaded automatically when running the compose file.

The **api** composition file includes the custom services providing the necessary APIs you have to use, including
the Data Distribution Service (dds) and the Search Service (search). You can find the Docker images in the
**images** folder. In order to use them, use the following commands:


```
docker load -i ./images/dds.tar
docker load -i ./images/search.tar
```

Both the postgres and the Elasticsearch containers are mounted to a volume on the local machine. If you are
working on Linux/macOS both volumes should be created inside **.volume** wherever your .yml files are
located. On Windows, a named volume is needed for the postgres container. This means if you want to
remove all data you have to remove both the **volume** folder as well as the named volume via the Docker CLI.

To get them all up and running, continue with the following steps:

1. Start all **persistence** containers:

```
docker compose -f ./service-composition/docker-compose-persistence.yml up
```

2. Before starting the other containers, you have to add the uuid postgres extension by executing the
following SQL command on the database:


```
CREATE EXTENSION IF NOT EXISTS "uuid-ossp";
```
An easy way to do this is to use the admin UI running on port 2080 with the following credentials:

```
System: PostgreSQL
Server: database (name of the container)
Username: root (according to env variable)
Password: postgres (according to env variable)
Database: dds_db
```

After a successful login you should be able to select **SQL command** on the left side, which guides you to a
small editor allowing you to execute SQL commands. You can also execute the command directly
inside the container (e.g. with docker exec) or use any other database administration tool.

3. Make sure Elasticsearch is up and running. You can use the curl command below to send a REST
request directly to the Elasticsearch instance. If the response has the status code 200 and the status
attribute in the JSON object is green or yellow, you should be ready to continue.
```
curl --request GET \
    --url 'http://localhost:9200/_cluster/health?pretty=true'
```

4. Start the remaining **api** containers:

```
docker compose -f ./service-composition/docker-compose-api.yml up
```
The Data Distribution Service should be able to create all necessary tables inside the postgres database
**dds_db**. The Search service should create an index called **learning-objects-dev**. You can check the
postgres database again via the admin UI and use the following curl command to see if the index has been
created:

```
curl --request GET \
    --url http://localhost:9200/learning-objects-dev/_search
```

5. Add the resource type with the name **LEARNING_OBJECT** in **resource_types** table inside the **dds_db**.


6. Add a provider to the table **providers** inside the **dds_db** (e.g.: name=Moodle). After adding a new
provider the dds has to be restarted. You can either stop and restart the whole composition or just the
dds.


7. Register the search service as consumer by using the **/consumer** endpoint

```
curl --request POST \
    --url http://localhost:5003/consumers \
    --header 'Content-Type: application/json' \
    --data '{
        "name": "Search Service",
        "endpoint": "http://search:5002/resources",
        "resources": [
            {
                "name": "LEARNING_OBJECT"
            }
        ]
}'
```

Now you should be able to test the whole flow by sending a resource (learning object) to the Data
Distribution Service, which should push all incoming learning objects to all registered consumers interested in
resources of the type **LEARNING_OBJECT**.

### Send Learning Objects to the Data Distribution Service
Learning Objects can be sent according to the following curl command (make sure to replace the provider ID
in the query parameter). The ID of a learning object has to be provided by you/moodle and
can be any string (Doesn't have to be a UUID). Description, syllabi, tags and groups are optional. This
metadata can be used to get specific search result. You have to provide them whenever you create a new
learning activity, in order to pass them to to the endpoint.

```
curl --request PUT \
  --url 'http://localhost:5003/learning-objects?provider_id=<PROVIDER_ID>' \
  --header 'Content-Type: application/json' \
  --data '[
    {
      "id": "d848cc93-cea8-469e-9a3d-e197dea608da",
      "title": "Fractions",
      "name": "Fractions",
      "description": "This activity contains a bunch of simple exercises about fractions",
      "syllabi": [
        "0adb6179-337c-4783-86fd-262b4bbf0736"
      ],
      "tags": [
        "Maths",
        "1st grade"
      ],
      "groups": [
        "Maths A"
      ]
    }
]'
```

### Search for Learning Objects

After the learning object has been successfully pushed to the Search Service, you can use the **/search**
endpoint for searching learning objects. The given query is used to search for any matching documents. The
syllabi array can be empty.

```
curl --request POST \
    --url http://localhost:5002/search \
    --header 'Content-Type: application/json' \
    --data '{
        "query": "fractions",
        "syllabi": [
    ]
}'
```

## Moodle Setup

To get you started with Moodle, you can also use Docker to quickly create an instance. The compose file uses
publicly available images from [Bitnami](https://bitnami.com/stack/moodle/containers). Running this for the first time can take a couple of minutes. Moodle
should be reachable at [http://localhost:8080.](http://localhost:8080.)

```
docker compose -f ./moodle-composition/docker-compose.yml up
```
# Recommended Learnings/ Block Implementation 
Recommended_Learnings/block_recommended_learnings.php 
```
class block_recommended_learnings extends block_list { 
    public function init() { 
        $this->title = get_string('pluginname','block_recommended_learnings'); 
    } 
 ```
 ```
 class block_recommended_learnings extends block_list { 
    }
 ```
##### Description 
Creates a new block class extending the pre-defined list class, meaning our data will be displayed in a list format. (This is not the intended final block type, however was the furthest working implementation.) 
##### Parameters
block_recommended_learnings: The new block class being created 
block list: The already existing block being extended 
##### What the function returns 
A basic list based block 

&nbsp;
```
public function init() { }
```
##### Description 
The init() function is implemented for all blocks and defines values for the title object. 
##### Parameters 
Public Function: A function that can be accessed anywhere with no restrictions 

&nbsp;
```
$this->title 
```
##### Description 
Defines the title of the current block 
##### Parameters
$this: refers to the current object in the block 
->: refers to a property of the current objects 
##### What this function returns 
A property of an object within the current block

&nbsp;
```	
Get_string('pluginname','block_recommended_learnings'); 
```
##### Description
Returns a string stored inside the lang/en directory. 
##### Parameters
title: The title of the block 
‘pluginname’: The link to the language folder, within the language folder there is a corresponding string that is called and displayed whenever this link is used within a get_string command 
'block_recommended_learnings': The name of the file in which the strings are stored 
##### What the functions return 
A pre-defined  string value 

&nbsp;
```	
public function get_content(){ 
        if ($this->content !== null) { 
            return $this->content; 
        } 
  
        global $DB; 

        $this->content = new stdClass; 
        $this->content->items[] = get_string('pluginname', 'block_recommended_learnings'); 
```
```
public function get_content(){ }
```
##### Description
A function for retrieving and displaying content for the block. 

&nbsp;
```
        if ($this->content !== null) { 
            return $this->content; 
        } 
```
##### Description
Checks if there is already content defined in the current block 
##### Parameters
$this->content: refers to the current object in the block 
##### What this function returns
If the content object is not null then the current content is processed and returned. 

&nbsp;
```
lobal $DB; 
```
##### Description
Instance of the global moodle_database class 
##### What this returns
The DB class will be used to access records later in the function.  

&nbsp;
```
        $this->content = new stdClass; 
        $this->content->items[] = get_string('pluginname', 'block_recommended_learnings'); 
```
##### Description
Creates a new Standard Moodle Class 
Within this new class creates a list object and stores a string as the first variable 
##### Parameters
stdClass: Standard Moodle Class a plain object with no pre-existing class formatting 
items[]: An list object within the content object 
##### What this code returns
This function does not return anything, it stores items in a list for later use. 

&nbsp;
```
//Returns current categories in course database, these will be used to match recommendations 
        $courses = get_courses(); 
        foreach ($courses as $id=>$course) { 
            $category = $DB->get_record ('course_categories', array('id'=>$course->category)); 
            $course->categoryName = $category->name; 
            $allcourses[$id] = $course; 
        } 
```
##### Description 
Finds the users active courses (get_courses) 
For every course returns the record from the course categories table 
Defines the Category name of the course 
Stores the course in a list by their id 
##### Parameters
$courses: the users active courses 
$id=>$course:  
  
&nbsp;
```
$category = $DB->get_record ('course_categories', array('id'=>$course->category)); 
```
##### Description 
Queries the records of course from the course categories table 
##### Parameters
course_categories: the course categories table 
array(…):the conditions of the sql search 
##### What this function returns
Returns a database record as an object if all conditions are met 

&nbsp;
```
        foreach ($allcourses as $id=>$course) { 
            $this->content->items[] = html_writer::tag('a', 
$course->categoryName, array('href' => '/blocks/testblock/some_file.php')); 
return $this->content; 
```
##### Description
Adds elements to the items list depending on the elements of the all courses list 
##### Parameters
$allcourses: The list created in the previous code segment containing the list of users courses 
html_writer: creates a hyperlink with certain, setting the text of the link to the category name of the current item in the allcourses list 
##### What this function returns 
Returns the list within the content object. 
As the block is from a block_list the list is unpacked and each item is displayed on a new line. 

&nbsp;
```
public function specialization() { 
        if (isset($this->config)) { 
            if (empty($this->config->title)) { 
                $this->title = get_string('defaulttitle', 'block_recommended_learnings'); 
            } else { 
                $this->title = $this->config->title; 
            } 

            if (empty($this->config->text)) { 
                $this->config->text = get_string('defaulttext', 'block_recommended_learnings'); 
            } 
        } 
    } 
```
##### Description
Allows for block to have its own configuration separate. 
Specialization is called directly after the init() function and changes the default                 
$this->title/$this->text to a title/text which has been edited in an edit_form.php document. 
##### Parameters 
$this->config->title: Title defined in the edit_form document 
$this->config->text: Text defined in the edit_form document 
##### What this function returns 
Any relevant changes made in the edit_form.php document 

#Writing custom search engine 

 

The methods that are implemented in the engine.php file: 

 
```
    public function is_installed(); 

    public function is_server_ready(); 

    public function add_document($document, $fileindexing = false); 

    public function get_query_total_count(); 

    public function execute_query($filters, $usercontexts, $limit = 0);  

    private function make_request($search); 

    public function delete($module = null); 

 ```

 

public function is_installed() always returns true, because Elastic Search only needs curl, and Moodle already requires it, so it is ok to just return true. 

 

Then public function is_server_ready(); It checks if your search engine is ready. And it returns true or false depending if it a get request to the server succeeds. 

 

public function add_document($document, $fileindexing = false); This method is executed when Moodle contents are being indexed in the search engine. 

$document will contain a document data with all required fields (+ maybe some optional fields) and its contents will be already validated so a integer field will come with an integer value 

$fileindexing will be true the search area that generated the document supports attached files otherwise it will return false. 

This method doesn’t return anything, it only creates request to the elastic search to save the data. 

execute_query($filters, $usercontexts, $limit = 0) constructs an array representing a prepared query created by the $filters parameter. It calls the private function make_request($search) where the $search with the parameters array. make_request sends a request to the elastic search server to fetch the search results, iterates through the results, checks if we have access for each result, converts the results to \core_search\document using \core_search\document::set_data_from_engine and returns an array of \core_search\document. That result array gets returned by execute_query.  

public function get_query_total_count() - returns the number of results that available for the most recent call to execute_query(). This is used to determine how many pages will be displayed in the paging bar. It returns MAX_RESULTS -  User will always see 10 pages, except when they are on the last page of actual results. It’s a really easy implementation but the user experience is not very good. 

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

