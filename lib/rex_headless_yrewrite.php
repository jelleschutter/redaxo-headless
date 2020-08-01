<?php

class rex_headless_yrewrite extends rex_yrewrite {

    public static function rewrite($params = [], $yparams = [], $fullpath = false)
    {
        // Url wurde von einer anderen Extension bereits gesetzt
        if (isset($params['subject']) && $params['subject'] != '') {
            return $params['subject'];
        }

        $id = $params['id'];
        $clang = $params['clang'];

        if (isset(self::$paths['redirections'][$id][$clang])) {
            $params['id'] = self::$paths['redirections'][$id][$clang]['id'];
            $params['clang'] = self::$paths['redirections'][$id][$clang]['clang'];
            return self::rewrite($params, $yparams, $fullpath);
        }

        $path = false;

        if (!$fullpath && isset(self::$paths['paths']['default'][$id][$clang])) {
            $path = self::$paths['paths']['default'][$id][$clang];
        }

        if ($path === false) {
            foreach ((array) self::$paths['paths'] as $i_domain => $i_id) {
                if (isset(self::$paths['paths'][$i_domain][$id][$clang])) {
                    $domain = self::getDomainByName($i_domain);
                    $path = $domain->getUrl() . self::$paths['paths'][$i_domain][$id][$clang];
                    break;
                }
            }
        }

        // params
        $urlparams = '';
        if (isset($params['params'])) {
            $urlparams = rex_string::buildQuery($params['params'], $params['separator']);
        }

        return $path . ($urlparams ? '?' . $urlparams : '');
    }

    public static function getArticleObjectByUrl($url)
    {
        foreach (self::$paths['paths']['default'] as $articleId => $articleObj) {
            foreach ($articleObj as $clangId => $articleUrl) {
                if ($url == $articleUrl) {
                    return [
                        'id' => $articleId,
                        'clang' => $clangId
                    ];
                }
            }
        }
        return false;
    }
}