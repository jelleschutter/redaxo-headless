<?php

class rex_headless_api_base extends rex_api_function {

    protected $published = true;

    function execute()
    {
        $addon = rex_addon::get('headless');
        if ($addon->getConfig('debug', false)) {
            rex_response::setHeader('Access-Control-Allow-Origin', '*');
        }
    }
}