<?php

class rex_headless_navigation {

    private $currentArticleId = -1;
    private $currentCategoryId = -1;
    private $path = [];

    public function __construct($articleId, $clangId = null)
    {
        if ($article = rex_article::get($articleId, $clangId)) {
            $path = trim($article->getPath(), '|');

            $this->path = [];
            if ('' != $path) {
                $this->path = explode('|', $path);
            }

            $this->currentArticleId = $articleId;
            $this->currentCategoryId = $article->getCategoryId();
            $this->currentClangId = $article->getClangId();
        } else {
            rex_response::setStatus(400);
            rex_response::sendJson([]);
        }
    }

    /**
     * @param int $categoryId
     * @param int $depth
     *
     * @return array
     */
    public function get($categoryId, $depth = 1)
    {
        if ($categoryId < 1) {
            $categories = array_merge(rex_article::getRootArticles(false, $this->currentClangId), rex_category::getRootCategories(false, $this->currentClangId));
        } else {
            $categories = array_merge(rex_category::get($categoryId, $this->currentClangId)->getArticles(), rex_category::get($categoryId, $this->currentClangId)->getChildren());
        }

        $list = [];
        foreach ($categories as $nav) {
            $item = [];
            $item['id'] = $nav->getId();
            $item['link'] = rex_yrewrite::rewrite(['id' => $nav->getId(), 'clang' => $nav->getClangId()]);
            $item['name'] = rex_escape($nav->getName());
            $item['current'] = ($nav->getId() == $this->currentArticleId || $nav->getId() == $this->currentCategoryId);
            $item['active'] = ($nav->getId() == $this->currentArticleId || in_array($nav->getId(), $this->path));

            if ($depth > 0 && $nav instanceof rex_category) {
                $children = $this->get($nav->getId(), $depth - 1);
                if (count($children) > 0) {
                    $item['children'] = $children;
                }
            }
            $list[] = $item;
        }
        if (count($list) > 0) {
            return $list;
        }
        return [];
    }
}