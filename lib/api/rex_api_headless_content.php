<?php

class rex_api_headless_content extends rex_base_api_headless {

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
        $articleClang = $articleMeta['clang'];

        $seo = new rex_yrewrite_seo($articleId, $articleClang);

        $articleContent = new rex_article_content($articleId, $articleClang);

        rex_extension::register('ART_CONTENT', function ($ep) {
            $dom = new DOMDocument();
            libxml_use_internal_errors(true);
            $dom->loadHTML($ep->getSubject(), LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
            libxml_clear_errors();
            $images = $dom->getElementsByTagName('img');

            foreach ($images as $image) {
                $oldSrc = $image->getAttribute('src');
                if (substr($oldSrc, 0, 1) === '/') {
                    $newSrc = rex::getServer() . $oldSrc;
                    $image->setAttribute('src', $newSrc);
                }
            }

            $ep->setSubject($dom->saveHTML());
        }, rex_extension::LATE);

        rex_extension::register('URL_REWRITE', function (rex_extension_point $ep) {
            $params = $ep->getParams();
            $params['subject'] = $ep->getSubject();
            return rex_headless_yrewrite::rewrite($params);
        }, rex_extension::EARLY);

        rex_response::sendJson([
            'meta' => [
                'title' => $seo->getTitle(),
                'description' => $seo->getDescription()
            ],
            'title' => $articleContent->getValue('name'),
            'content' => $articleContent->getArticleTemplate()
        ]);
    }
}