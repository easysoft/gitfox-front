<?php
global $app, $lang;
$config->variable->actionList = array();
$config->variable->actionList['edit']['icon'] = 'edit';
$config->variable->actionList['edit']['text'] = $lang->variable->edit;
$config->variable->actionList['edit']['hint'] = $lang->variable->edit;
$config->variable->actionList['edit']['url']  = array('module' => 'variable', 'method' => 'edit', 'params' => 'id={id}');

$config->variable->actionList['delete']['icon']         = 'trash';
$config->variable->actionList['delete']['text']         = $lang->variable->delete;
$config->variable->actionList['delete']['hint']         = $lang->variable->delete;
$config->variable->actionList['delete']['url']          = array('module' => 'variable', 'method' => 'delete', 'params' => 'id={id}');
$config->variable->actionList['delete']['className']    = 'ajax-submit';
$config->variable->actionList['delete']['data-confirm'] = array('message' => $lang->variable->notice->confirmDelete, 'icon' => 'icon-exclamation-sign', 'iconClass' => 'warning-pale rounded-full icon-2x');
