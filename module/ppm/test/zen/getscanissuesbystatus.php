#!/usr/bin/env php
<?php
include dirname(__FILE__, 5) . '/test/lib/init.php';
include dirname(__FILE__, 2) . '/lib/zen.class.php';

zenData('entry')->loadYaml('entry', false, 2)->gen(1);
zenData('user')->gen(3);
zendata('ops_scan_issues')->gen(0);

$scanIssue = zendata('ops_scan_issues');
$scanIssue->id->range('1-4');
$scanIssue->issueKey->range('ppm-zen-1,ppm-zen-2,ppm-zen-3,ppm-zen-ai');
$scanIssue->repoID->range('42{4}');
$scanIssue->ppmID->range('81{4}');
$scanIssue->scanMethod->range('check,smell,check,ai');
$scanIssue->status->range('wait,solved,wait,wait');
$scanIssue->deleted->range('0{4}');
$scanIssue->gen(4);

/**

title=测试 ppmZen->getScanIssuesByStatus();
timeout=0
cid=0

- 执行test模块的getScanIssuesByStatusTest方法，参数是'', 42, 81, $pager  @1
- 执行test模块的getScanIssuesByStatusTest方法，参数是'', 42, 81, $pager  @3
- 执行test模块的getScanIssuesByStatusTest方法，参数是'wait', 42, 81, $pager  @2
- 执行$pager->recTotal @2
- 执行test模块的getScanIssuesByStatusTest方法，参数是'solved', 0, 0, $pager  @1
- 执行test模块的getScanIssuesByStatusTest方法，参数是'closed', 42, 0, new pager  @0
- 执行test模块的getScanIssuesByStatusTest方法，参数是'', 42, 81, new pager  @1

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
r(count((array)$test->getScanIssuesByStatusTest('', 42, 81, $pager))) && p() && e('3');
r(count((array)$test->getScanIssuesByStatusTest('wait', 42, 81, $pager))) && p() && e('2');
r($pager->recTotal) && p() && e('2');
r(count((array)$test->getScanIssuesByStatusTest('solved', 0, 0, $pager))) && p() && e('1');
r(count((array)$test->getScanIssuesByStatusTest('closed', 42, 0, new pager(0, 20, 1)))) && p() && e('0');
r(count((array)$test->getScanIssuesByStatusTest('', 42, 81, new pager(0, 2, 2)))) && p() && e('1');