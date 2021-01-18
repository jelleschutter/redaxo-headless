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

echo rex_view::title(rex_i18n::msg('headless'));

rex_be_controller::includeCurrentPageSubPath();
