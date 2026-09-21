#!/usr/bin/env php
<?php
include dirname(__FILE__, 5) . '/test/lib/init.php';
include dirname(__FILE__, 2) . '/lib/zen.class.php';

zenData('entry')->loadYaml('entry', false, 2)->gen(1);
zenData('user')->gen(3);

/**

title=测试 ppmZen->getScanIssuesByStatus();
timeout=0
cid=0

- 按状态取静态扫描问题返回数组  @1
- 接口不可达时返回空列表  @0
- 状态 wait 返回空列表  @0
- 状态 bug（仅 devops 扩展接管）返回空列表  @0
- 空数据时 pager 的总数不被改写  @0
- 仓库与评审传 0 时仍返回数组  @1
- 评审传 0 时返回空列表  @0
- 第二页仍返回数组  @1

*/

global $app;
$app->loadClass('pager', true);
$app->rawModule = 'ppm';
$app->rawMethod = 'browse';
$app->setMethodName('browse');

su('admin');
$test  = new ppmZenTest();
$pager = new pager(0, 20, 1);

r(is_array($test->getScanIssuesByStatusTest('', 42, 81, $pager))) && p() && e('1');
r(count((array)$test->getScanIssuesByStatusTest('', 42, 81, $pager))) && p() && e('0');
r(count((array)$test->getScanIssuesByStatusTest('wait', 42, 81, $pager))) && p() && e('0');
r(count((array)$test->getScanIssuesByStatusTest('bug', 42, 81, $pager))) && p() && e('0');
r($pager->recTotal) && p() && e('0');
r(is_array($test->getScanIssuesByStatusTest('solved', 0, 0, $pager))) && p() && e('1');
r(count((array)$test->getScanIssuesByStatusTest('closed', 42, 0, new pager(0, 20, 1)))) && p() && e('0');
r(is_array($test->getScanIssuesByStatusTest('ignore', 42, 81, new pager(0, 20, 2)))) && p() && e('1');
