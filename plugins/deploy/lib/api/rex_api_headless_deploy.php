<?php

class rex_api_headless_deploy extends rex_api_function {

    protected $published = true;

    function execute()
    {
        $addon = rex_addon::get('headless');
        $plugin = $addon->getPlugin('deploy');

        $oldFiles = self::getAllFiles($plugin->getAssetsPath());

        if ($plugin->getConfig('enable_deploy', '0') !== '1') {
            rex_response::setStatus(403);
            rex_response::sendJson([
                'msg' => 'Deploy disabled!'
            ]);
            exit;
        }

        if (rex_post('token', 'string', null) !== $plugin->getConfig('token', '')) {
            rex_response::setStatus(403);
            rex_response::sendJson([
                'msg' => 'Invalid Token!'
            ]);
            exit;
        }

        $files = rex_files('file', 'array', null);

        if ($files === null) {
            rex_response::setStatus(400);
            rex_response::sendJson([
                'msg' => 'Missing ZIP file!'
            ]);
            exit;
        }

        $fileName = $files['tmp_name'];

        $zip = new ZipArchive();
        $zip->open($fileName);

        $spaHtml = false;

        for ($i = 0; $i < $zip->numFiles; $i++) {
            $file = $zip->statIndex($i);
            if ($file['name'] == 'index.html') {
                $html = $zip->getFromIndex($i);

                $dom = new DOMDocument();
                libxml_use_internal_errors(true);
                $dom->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'), LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
                libxml_clear_errors();


                $scripts = $dom->getElementsByTagName('script');
                foreach ($scripts as $script) {
                    $src = $script->getAttribute('src');
                    if (substr($src, 0, 1) === '/') {
                        $script->setAttribute('src', $plugin->getAssetsUrl(substr($src, 1)));
                    }
                }

                $links = $dom->getElementsByTagName('link');
                foreach ($links as $link) {
                    $href = $link->getAttribute('href');
                    if (substr($href, 0, 1) === '/') {
                        $link->setAttribute('href', $plugin->getAssetsUrl(substr($href, 1)));
                    }
                }

                $spaHtml = $dom->saveHTML($dom->documentElement);
            } else {
                $newPath = $plugin->getAssetsPath($file['name']);
                $newDir = pathinfo($newPath, PATHINFO_DIRNAME);
                if (!is_dir($newDir)) {
                    rex_dir::create($newDir);
                }
                if (($key = array_search($newPath, $oldFiles)) !== false) {
                    unset($oldFiles[$key]);
                }
                file_put_contents($newPath, $zip->getFromIndex($i));
            }
        }
        $zip->close();

        if ($spaHtml !== false) {
            $addon->setConfig('spa_html', $spaHtml);

            // remove old files
            foreach ($oldFiles as $file) {
                unlink($file);
            }

            $emptyDirs = self::getEmptyDirectories($plugin->getAssetsPath());
            foreach ($emptyDirs as $emptyDir) {
                rmdir($emptyDir);
            }

            rex_response::sendJson([
                'msg' => 'Successfully updated content!'
            ]);
        } else {
            rex_response::sendJson([
                'msg' => 'Missing \'index.html\'!'
            ]);
        }
        exit;
    }

    private static function getAllFiles($path = '') {
        $dir = [];
        $dirContent = scandir($path);
        $dirContent = array_filter($dirContent, static function ($value) {
            return !in_array($value, array('.', '..'));
        });
        foreach ($dirContent as $value) {
            $newPath = $path . $value;
            if (is_dir($newPath)) {
                $dir = array_merge($dir, self::getAllFiles($newPath . DIRECTORY_SEPARATOR));
            } else {
                $dir[] = $newPath;
            }
        }
        return $dir;
    }

    private static function getEmptyDirectories($path = '') {
        $dir = [];
        $subDirs = scandir($path);
        $subDirs = array_filter($subDirs, static function ($value) {
            return !in_array($value, array('.', '..'));
        });
        $notEmpty = count($subDirs);
        foreach ($subDirs as $value) {
            $newPath = $path . $value;
            if (is_dir($newPath)) {
                $subDir = self::getEmptyDirectories($newPath . DIRECTORY_SEPARATOR);
                if ($subDir === true) {
                    $notEmpty--;
                    $dir[] = $newPath . DIRECTORY_SEPARATOR;
                } else {
                    $dir = array_merge($dir, $subDir);
                }
            }
        }
        if ($notEmpty == 0) {
            return true;
        }
        return $dir;
    }
}