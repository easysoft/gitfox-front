#!/usr/bin/env php
<?php

/**

title=测试 commonModel::isDevopsHomeMenuPage();
timeout=0
cid=0

- 步骤1：评审请求列表页属于应用二级菜单页面 @1
- 步骤2：扫描任务列表页属于应用二级菜单页面 @1
- 步骤3：静态扫描问题页属于应用二级菜单页面 @1
- 步骤4：代码库内的人工审查问题页属于代码库菜单页面 @0
- 步骤5：代码库内的代码评审页属于代码库菜单页面 @0
- 步骤6：代码库列表页属于应用二级菜单页面 @1
- 步骤7：按空间和代码库筛选的全部代码库列表属于应用二级菜单页面 @1

*/

include dirname(__FILE__, 5) . '/test/lib/init.php';
include dirname(__FILE__, 2) . '/lib/model.class.php';

zenData('company')->gen(1);
zenData('user')->gen(1);
su('admin');

$commonTester = new commonModelTest();

$commonTester->setDevopsRequestTest('ppm', 'browse', array('repoID' => 0));
r($commonTester->isDevopsHomeMenuPageTest()) && p() && e('1'); // 步骤1：评审请求列表页属于应用二级菜单页面
$commonTester->setDevopsRequestTest('codescan', 'task', array('repoID' => 0));
r($commonTester->isDevopsHomeMenuPageTest()) && p() && e('1'); // 步骤2：扫描任务列表页属于应用二级菜单页面
$commonTester->setDevopsRequestTest('codescan', 'issue', array('repoID' => 0));
r($commonTester->isDevopsHomeMenuPageTest()) && p() && e('1'); // 步骤3：静态扫描问题页属于应用二级菜单页面
$commonTester->setDevopsRequestTest('repo', 'review', array('repoID' => 5));
r($commonTester->isDevopsHomeMenuPageTest()) && p() && e('0'); // 步骤4：代码库内的人工审查问题页属于代码库菜单页面
$commonTester->setDevopsRequestTest('ppm', 'browse', array('repoID' => 5));
r($commonTester->isDevopsHomeMenuPageTest()) && p() && e('0'); // 步骤5：代码库内的代码评审页属于代码库菜单页面
$commonTester->setDevopsRequestTest('repo', 'maintain', array('repoID' => 0));
r($commonTester->isDevopsHomeMenuPageTest()) && p() && e('1'); // 步骤6：代码库列表页属于应用二级菜单页面
$commonTester->setDevopsRequestTest('ppm', 'browse', array('repoID' => 5, 'spaceID' => 2, 'allRepo' => 1));
r($commonTester->isDevopsHomeMenuPageTest()) && p() && e('1'); // 步骤7：按空间和代码库筛选的全部代码库列表属于应用二级菜单页面
