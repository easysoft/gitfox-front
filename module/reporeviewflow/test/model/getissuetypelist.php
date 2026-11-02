#!/usr/bin/env php
<?php
include dirname(__FILE__, 5) . '/test/lib/init.php';
include dirname(__FILE__, 2) . '/lib/model.class.php';

/**

title=测试 reporeviewflowModel::getIssueTypeList();
timeout=0
cid=0

- 获取的问题类型列表为数组 @1
- 问题类型列表不包含全部类型 @0
- 问题类型列表包含四种具体问题类型 @4
- 问题类型列表的键顺序和内容正确 @defect,security,compliance,optimize
- 问题类型列表的展示文案正确 @缺陷;安全;合规性;优化
- 获取问题类型列表不会删除语言配置中的全部类型 @1

*/

global $lang;

zenData('user')->gen(1);
su('admin');

$test = new reporeviewflowTest();

$types = $test->getIssueTypeListTest();
r(is_array($types) ? 1 : 0) && p() && e('1'); // 获取的问题类型列表为数组

r(isset($types['all']) ? 1 : 0) && p() && e('0'); // 问题类型列表不包含全部类型

r(count($types)) && p() && e('4'); // 问题类型列表包含四种具体问题类型

r(implode(',', array_keys($types))) && p() && e('defect,security,compliance,optimize'); // 问题类型列表的键顺序和内容正确

r($types) && p('defect;security;compliance;optimize') && e('缺陷;安全;合规性;优化'); // 问题类型列表的展示文案正确

r(isset($lang->codescan->typeList['all']) ? 1 : 0) && p() && e('1'); // 获取问题类型列表不会删除语言配置中的全部类型
