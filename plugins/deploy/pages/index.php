<?php

/**
 * Headless Addon.
 *
 * @author jelle@schutter.xyz
 *
 * @package redaxo\headless\deploy
 *
 * @var rex_plugin $this
 */

$form = rex_config_form::factory($this->getPackageId());

$field = $form->addSelectField('enable_deploy', null, ['class' => 'form-control']);
$field->setLabel($this->i18n('enable_deploy'));
$select = new rex_select();
$select->addOption('Enabled', true);
$select->addOption('Disabled', false);
$field->setSelect($select);

$field = $form->addTextField('token', null, ['class' => 'form-control']);
$field->setNotice(str_replace('{token}', bin2hex(random_bytes((32-(32%2))/2)), $this->i18n('token_note')));
$field->setLabel($this->i18n('token'));
$field->setAttribute('auto_complete', false);

$fragment = new rex_fragment();
$fragment->setVar('class', 'edit', false);
$fragment->setVar('title', $this->i18n('config'), false);
$fragment->setVar('body', $form->get() , false);
echo $fragment->parse('core/page/section.php');
