<?php

class rex_base_api_headless extends rex_api_function {

    protected $published = true;

    function execute()
    {
        rex_response::setHeader('Access-Control-Allow-Origin', '*');
    }
}