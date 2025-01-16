<?php

class MY_Router extends CI_Router {
    protected function _set_request($segments = []) {
        parent::_set_request($segments);
        log_message('debug', 'Matched route: ' . implode('/', $segments));
    }
}