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

        rex_response::sendJson([
            'meta' => [
                'title' => $seo->getTitle(),
                'description' => $seo->getDescription()
            ],
            'title' => $articleContent->getValue('name'),
            'content' => $articleContent->getArticle()
        ]);
    }
}