<?php
$config->variable->dtable = new stdclass();
$config->variable->dtable->fieldList['id']['title'] = $lang->idAB;
$config->variable->dtable->fieldList['id']['type']  = 'id';

$config->variable->dtable->fieldList['name']['title'] = $lang->variable->name;
$config->variable->dtable->fieldList['name']['type']  = 'shortTitle';
$config->variable->dtable->fieldList['name']['hint']  = true;

$config->variable->dtable->fieldList['key']['title']    = $lang->variable->key;
$config->variable->dtable->fieldList['key']['sortType'] = false;
$config->variable->dtable->fieldList['key']['hint']     = true;

$config->variable->dtable->fieldList['type']['title']    = $lang->variable->type;
$config->variable->dtable->fieldList['type']['sortType'] = true;
$config->variable->dtable->fieldList['type']['hint']     = true;
$config->variable->dtable->fieldList['type']['width']    = 100;
$config->variable->dtable->fieldList['type']['map']      = $lang->variable->typeList;

$config->variable->dtable->fieldList['createdDate']['title']      = $lang->variable->createdDate;
$config->variable->dtable->fieldList['createdDate']['sortType']   = true;
$config->variable->dtable->fieldList['createdDate']['hint']       = true;
$config->variable->dtable->fieldList['createdDate']['type']       = 'datetime';
$config->variable->dtable->fieldList['createdDate']['formatDate'] = 'YYYY-MM-dd hh:mm';

$config->variable->dtable->fieldList['actions']['name']  = 'actions';
$config->variable->dtable->fieldList['actions']['title'] = $lang->actions;
$config->variable->dtable->fieldList['actions']['type']  = 'actions';
$config->variable->dtable->fieldList['actions']['width'] = '100';
$config->variable->dtable->fieldList['actions']['menu']  = array('edit', 'delete');
$config->variable->dtable->fieldList['actions']['list']  = $this->config->variable->actionList;
