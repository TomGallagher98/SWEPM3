<?php

namespace elastic;

class engine extends \core_search\engine {

    public function is_server_ready() {
        global $CFG;
        require_once($CFG->dirroot.'/lib/filelib.php');
        $c = new \curl();
        return (bool)json_decode($c->get($this->config->server_hostname . ":" . $this->config->server_port));
    }

    public function add_document(array $doc, $fileindexing=false) {
        $document = $doc->export_for_engine();
        $url = $this->config->server_hostname . ":" . $this->config->server_port . '/' . $this->config->indexname . '?provider_id=9fcc8636-4fbc-4fa9-983c-a38768d7d554';

        $jsondoc = json_encode($document);

        $curl = new \curl();
        $curl->put($url, $jsondoc);
    }

    public function execute_query($filters, $usercontexts, $limit=0) {
        $serverstatus = $this->is_server_ready();
        if ($serverstatus !== true) {
            throw new \core_search\engine_exception('engineserverstatus', 'search');
        }
 
        if (empty($limit)) {
            $limit = $this->get_query_total_count();
        } 
        
        $search = array('query' => array('bool' => array('must' => array(array('match' => array('content' => $filters->q))))));

        $url = $this->config->server_hostname . ":" . $this->config->server_port . '/'
                . $this->config->indexname . '/_search?pretty';

        $c = new \curl();
        $results = json_decode($c->post($url, json_encode($search)));
        $docs = array();
        if (isset($results->hits)) {
            $numgranted = 0;
            foreach ($results->hits->hits as $r) {
                if (!$searcharea = $this->get_search_area($r->_source->areaid)) {
                    continue;
                }
                $access = $searcharea->check_access($r->_source->itemid);
                switch ($access) {
                    case \core_search\manager::ACCESS_DELETED:
                    case \core_search\manager::ACCESS_DENIED:
                      continue;
                    case \core_search\manager::ACCESS_GRANTED:
                        $numgranted++;
                        $docs[] = $this->to_document($searcharea, (array)$r->_source);
                        break;
                }
            }
        } else {
            if (!$results) {
                return false;
            } else {
                throw new \core_search\engine_exception('connectionerror', 'search_elasticsearch', '', $results->error,
                                                        'Error type: ' . $results->error->type . ' - Reason: '
                                                            . $results->error->reason . ' - index: ' . $results->error->index );
            }
            return false;
        }
        return $docs;

    }

    public function get_query_total_count() {
        return \core_search\manager::MAX_RESULTS;
    }

    public function delete($module=null) {
        if (!$module) {
            $url = $this->config->server_hostname . ":" . $this->config->server_port . '/' . $this->config->indexname . '/?pretty';
            $c = new \curl();
            if ($response = json_decode($c->delete($url))) {
                if ( (isset($response->acknowledged) && ($response->acknowledged == true)) ||
                     ($response->status == 404)) {
                    return true;
                } else {
                    return false;
                }
            } else {
                return false;
            }
        }
    }
}
?>