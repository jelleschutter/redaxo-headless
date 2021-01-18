<?php

/**
 * Headless Addon.
 *
 * @author jelle@schutter.xyz
 *
 * @package redaxo\headless
 *
 * @var rex_addon $this
 */

if (rex::isFrontend() && $this->getConfig('override_output') && !rex_get('rex-api-call')) {
    rex_extension::register('ART_INIT', function () {
        rex_response::sendContent($this->getConfig('spa_html', ''));
    });
}