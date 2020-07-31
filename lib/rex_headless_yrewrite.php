<?php

class rex_headless_yrewrite extends rex_yrewrite {

    public static function getArticleObjectByUrl($domain, $url)
    {
        if ($domain instanceof rex_yrewrite_domain) {
            $domain = $domain->getName();
        }
        foreach (self::$paths['paths'][$domain] as $c_article_id => $c_o) {
            foreach ($c_o as $c_clang => $c_url) {
                if ($url == $c_url) {
                    return [
                        'id' => $c_article_id,
                        'clang' => $c_clang
                    ];
                }
            }
        }
        return false;
    }
}