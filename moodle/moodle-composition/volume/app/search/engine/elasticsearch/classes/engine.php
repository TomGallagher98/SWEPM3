<?php

namespace search_elasticsearch;

defined('MOODLE_INTERNAL') || die();

class engine extends \core_search\engine {

    public function is_installed() {
        return true;
    }

    public function is_server_ready() {
        global $CFG;
        require_once($CFG->dirroot.'/lib/filelib.php');
        $c = new \curl();
        return (bool)json_decode($c->get($this->config->server_hostname . ":" . $this->config->server_port));
    }

    public function add_document($document, $fileindexing = false) {
        $doc = $document->export_for_engine();
        $url = $this->config->server_hostname . ":" . $this->config->server_port . '/' . $this->config->indexname . '/'.$doc['id'];

        $jsondoc = json_encode($doc);

        $c = new \curl();
        $c->post($url, $jsondoc);
    }

    public function commit() {
    }

    public function optimize() {
    }

    public function post_file() {
    }

    public function get_query_total_count() {
        return \core_search\manager::MAX_RESULTS;
    }

    public function execute_query($filters, $usercontexts, $limit = 0) {

        $search = array('query' => array('bool' => array('must' => array(array('match' => array('content' => $filters->q))))));

        return $this->make_request($search);
    }

    /**
     *
     */
    private function make_request($search) {
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

    public function get_more_like_this_text($text) {

        $search = array('query' => array('more_like_this' => array('fields' => array('content'), 'like_text' => $text,
                                                                   'min_term_freq' => 1, 'max_query_terms' => 12)));
        return $this->make_request($search);
    }

    public function delete($module = null) {
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