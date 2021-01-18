<?php

class rex_api_headless_nav extends rex_api_function {

    protected $published = true;

    function execute()
    {
        $path = rex_request('path', 'string', false);
        if ($path === false) {
            rex_response::setStatus(400);
            rex_response::sendJson([]);
        }

        // $articleMeta = rex_headless_yrewrite::getArticleObjectByUrl($path);
        $domainName = rex_yrewrite::getHost();
        $domain = rex_yrewrite::getDomainByName($domainName);
        if ($domain === null) {
            $domain = rex_yrewrite::getDefaultDomain();
        }
        $articleMeta = rex_yrewrite::getArticleIdByUrl($domain, $path);

        if ($articleMeta === false) {
            rex_response::setStatus(400);
            rex_response::sendJson([]);
        }

        $articleId = array_keys($articleMeta)[0];
        $articleClang = $articleMeta[$articleId];

        $navInstance = new rex_headless_navigation($articleId, $articleClang);

        $levels = rex_request('levels', 'int', 2);


        $nav = $navInstance->get(0, $levels);

        rex_response::sendJson($nav);
    }
}