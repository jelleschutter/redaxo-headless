<?php

class rex_api_headless_nav extends rex_base_api_headless {

    function execute()
    {
        parent::execute();

        $path = rex_request('path', 'string', false);
        if ($path === false) {
            rex_response::setStatus(400);
            rex_response::sendJson([]);
        }

        $articleMeta = rex_headless_yrewrite::getArticleObjectByUrl($path);

        if ($articleMeta === false) {
            rex_response::setStatus(400);
            rex_response::sendJson([]);
        }

        $articleId = $articleMeta['id'];

        $navInstance = new rex_headless_navigation($articleId);

        $levels = rex_request('levels', 'int', 2);


        $nav = $navInstance->get(0, $levels);

        rex_response::sendJson($nav);
    }
}