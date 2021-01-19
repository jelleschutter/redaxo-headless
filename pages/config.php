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

$form = rex_config_form::factory($this->getName());

$field = $form->addSelectField('debug', null, ['class' => 'form-control']);
$field->setLabel($this->i18n('debug'));
$field->setNotice($this->i18n('debug_note'));
$select = new rex_select();
$select->addOption('Enabled', true);
$select->addOption('Disabled', false);
$field->setSelect($select);

$field = $form->addSelectField('override_output', null, ['class' => 'form-control']);
$field->setLabel($this->i18n('override_output'));
$select = new rex_select();
$select->addOption('Enabled', true);
$select->addOption('Disabled', false);
$field->setSelect($select);

$field = $form->addTextAreaField('spa_html', null, ['class' => 'form-control']);
$field->setLabel($this->i18n('spa_html'));
$field->setNotice($this->i18n('spa_html_note'));

$fragment = new rex_fragment();
$fragment->setVar('class', 'edit', false);
$fragment->setVar('title', $this->i18n('config'), false);
$fragment->setVar('body', $form->get() , false);
echo $fragment->parse('core/page/section.php');
