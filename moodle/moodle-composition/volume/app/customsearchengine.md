# Writing custom search engine 

 

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
