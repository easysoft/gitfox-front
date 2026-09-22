<?php
$lang->variable->browse = '浏览变量';
$lang->variable->create = '添加变量';
$lang->variable->edit   = '编辑变量';
$lang->variable->delete = '删除变量';

$lang->variable->name        = '名称';
$lang->variable->type        = '类型';
$lang->variable->key         = 'Key';
$lang->variable->value       = '值';
$lang->variable->desc        = '描述';
$lang->variable->createdBy   = '创建人';
$lang->variable->createdDate = '创建时间';

$lang->variable->typeList = array();
$lang->variable->typeList['plain']  = '明文';
$lang->variable->typeList['cipher'] = '密文';

$lang->variable->notice = new stdclass();
$lang->variable->notice->confirmDelete = '删除后引用该变量的流水线将无法使用该变量，是否确认删除？';
$lang->variable->notice->emptyProvider = '暂无变量。';

$lang->variable->placeholder = new stdclass();
$lang->variable->placeholder->name  = '只允许输入中英文,数字,下划线,连字符';
$lang->variable->placeholder->key   = '只允许输入英文,数字';
$lang->variable->placeholder->value = '只允许输入英文,数字,下划线,连字符';
