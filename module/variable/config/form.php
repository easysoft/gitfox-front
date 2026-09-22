<?php
$now = helper::now();

$config->variable->form = new stdclass();
$config->variable->form->create = array();
$config->variable->form->create['name']        = array('type' => 'string', 'required' => true, 'default' => '', 'filter' => 'trim');
$config->variable->form->create['type']        = array('type' => 'string', 'required' => true, 'default' => 'plain');
$config->variable->form->create['key']         = array('type' => 'string', 'required' => true, 'default' => '', 'filter' => 'trim');
$config->variable->form->create['value']       = array('type' => 'string', 'required' => true, 'default' => '', 'filter' => 'trim');
$config->variable->form->create['desc']        = array('type' => 'string', 'required' => false, 'default' => '', 'filter' => 'trim');
$config->variable->form->create['createdDate'] = array('type' => 'string', 'required' => false, 'default' => $now);

$config->variable->form->edit = $config->variable->form->create;
$config->variable->form->create['editedDate'] = array('type' => 'string', 'required' => false, 'default' => $now);
unset($config->variable->form->edit['createdDate']);
