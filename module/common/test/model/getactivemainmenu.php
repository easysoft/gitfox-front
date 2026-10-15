#!/usr/bin/env php
<?php

include dirname(__FILE__, 5) . '/test/lib/init.php';
include dirname(__FILE__, 2) . '/lib/model.class.php';

error_reporting(E_ERROR);

$zd_user = zenData('user');
$zd_user->id->range('1-1');
$zd_user->account->range('1-1');
$zd_user->last->range('(M)-(w)')->type('timestamp')->format('YYYY-MM-DD hh:mm:ss');
$zd_user->feedback->range('0');
$zd_user->scoreLevel->range('0');
$zd_user->resetExpired->range('0');
$zd_user->jira->range('0');
$zd_user->deleted->range('0');
$zd_user->gen(1);

su('admin');

$tester->loadModel('common');


$testObj = new commonModelTest();
/**

title=测试 commonModel::getActiveMainMenu()
timeout=0
cid=0

- 步骤1：正常输入 @1
- 步骤2：边界值输入 @1
- 步骤3：无效输入 @1
- 步骤4：大值输入 @1
- 步骤5：业务规则验证 @1
- 步骤6：扫描任务列表页的激活菜单为扫描 @codescan
- 步骤7：静态扫描问题页的激活菜单为问题 @issue
- 步骤8：人工审查问题页的激活菜单为问题 @issue
- 步骤9：AI评审配置页的激活菜单为AI评审 @aiCodeReview
- 步骤10：评审请求列表页的激活菜单为评审请求 @ppmList
- 步骤11：代码库内的代码评审页使用代码库的代码评审菜单 @ppm

*/

$result = $testObj->getActiveMainMenuTest();
r($result === '') && p() && e('1'); // 步骤1：正常输入
$result = $testObj->getActiveMainMenuTest();
r($result === '') && p() && e('1'); // 步骤2：边界值输入
$result = $testObj->getActiveMainMenuTest();
r($result === '') && p() && e('1'); // 步骤3：无效输入
$result = $testObj->getActiveMainMenuTest();
r($result === '') && p() && e('1'); // 步骤4：大值输入
$result = $testObj->getActiveMainMenuTest();
r($result === '') && p() && e('1'); // 步骤5：业务规则验证

zenData('company')->gen(1);

$zd_admin = zenData('user');
$zd_admin->id->range('2-2');
$zd_admin->account->range('admin');
$zd_admin->gen(1);

su('admin');

$testObj->setDevopsRequestTest('codescan', 'task', array('repoID' => 0));
r($testObj->getActiveMainMenuTest()) && p() && e('codescan'); // 步骤6：扫描任务列表页的激活菜单为扫描
$testObj->setDevopsRequestTest('codescan', 'issue', array('repoID' => 0));
r($testObj->getActiveMainMenuTest()) && p() && e('issue'); // 步骤7：静态扫描问题页的激活菜单为问题
$testObj->setDevopsRequestTest('repo', 'review', array('repoID' => 0));
r($testObj->getActiveMainMenuTest()) && p() && e('issue'); // 步骤8：人工审查问题页的激活菜单为问题
$testObj->setDevopsRequestTest('ppm', 'modelsetting', array('repoID' => 0));
r($testObj->getActiveMainMenuTest()) && p() && e('aiCodeReview'); // 步骤9：AI评审配置页的激活菜单为AI评审
$testObj->setDevopsRequestTest('ppm', 'browse', array('repoID' => 0));
r($testObj->getActiveMainMenuTest()) && p() && e('ppmList'); // 步骤10：评审请求列表页的激活菜单为评审请求
$testObj->setDevopsRequestTest('ppm', 'browse', array('repoID' => 1), 1, true);
r($testObj->getActiveMainMenuTest()) && p() && e('ppm'); // 步骤11：代码库内的代码评审页使用代码库的代码评审菜单
