#!/usr/bin/env php
<?php

/**

title=测试 ppmModel::getStaticScanIssues();
timeout=0
cid=0

- 执行$all @3
- 执行$pager1->recTotal @3
- 执行ppmModel模块的getStaticScanIssuesTest方法，参数是42, $pagerWait, 81, 'wait'  @2
- 执行ppmModel模块的getStaticScanIssuesTest方法，参数是42, $pagerSolved, 81, 'solved'  @1
- 执行$all, 'scanMethod' @smell,smell,check

- 执行ppmModel模块的getStaticScanIssuesTest方法，参数是42, $pagerRepo, 0, 'all'  @4
- 执行ppmModel模块的getStaticScanIssuesTest方法，参数是42, $pagerPage2, 81, 'all'  @1
- 执行$pagerPage2->recTotal @3
- 执行ppmModel模块的getStaticScanIssuesTest方法，参数是42, $pagerBug, 81, 'bug'  @1

*/

include dirname(__FILE__, 5) . '/test/lib/init.php';
include dirname(__FILE__, 2) . '/lib/model.class.php';

global $app, $tester;
$app->loadClass('pager', true);
$app->rawModule = 'ppm';
$app->rawMethod = 'browse';
$app->setMethodName('browse');

zendata('ops_scan_issues')->gen(0);

$scanIssue = zendata('ops_scan_issues');
$scanIssue->id->range('1-8');
$scanIssue->issueKey->range('ppm-static-1,ppm-static-2,ppm-static-3,ppm-ai-1,ppm-manual-1,ppm-static-4,ppm-static-5,ppm-static-deleted');
$scanIssue->repoID->range('42,42,42,42,42,42,99,42');
$scanIssue->ppmID->range('81,81,81,81,81,82,81,81');
$scanIssue->scanMethod->range('check,smell,smell,ai,manual,check,check,check');
$scanIssue->status->range('wait,solved,wait,wait,wait,wait,wait,wait');
$scanIssue->deleted->range('0,0,0,0,0,0,0,1');
$scanIssue->gen(8);

$tester->dao->delete()->from(TABLE_BUG)->where('id')->eq(100)->exec();
$tester->dao->insert(TABLE_BUG)->data((object)array('id' => 100, 'issueKey' => '1', 'deleted' => 0))->exec();

su('admin');

$ppmModel = new ppmModelTest();
$pager1   = new pager(0, 20, 1);
$pager2   = new pager(0, 20, 2);

$all = $ppmModel->getStaticScanIssuesTest(42, $pager1, 81, 'all');
r(count($all)) && p() && e('3');
r($pager1->recTotal) && p() && e('3');

$pagerWait = new pager(0, 20, 1, 'wait');
r(count($ppmModel->getStaticScanIssuesTest(42, $pagerWait, 81, 'wait'))) && p() && e('2');

$pagerSolved = new pager(0, 20, 1, 'solved');
r(count($ppmModel->getStaticScanIssuesTest(42, $pagerSolved, 81, 'solved'))) && p() && e('1');

r(implode(',', array_column($all, 'scanMethod'))) && p() && e('smell,smell,check');

$pagerRepo = new pager(0, 20, 1, 'repo');
r(count($ppmModel->getStaticScanIssuesTest(42, $pagerRepo, 0, 'all'))) && p() && e('4');

$pagerPage2 = new pager(0, 2, 2, 'page2');
r(count($ppmModel->getStaticScanIssuesTest(42, $pagerPage2, 81, 'all'))) && p() && e('1');
r($pagerPage2->recTotal) && p() && e('3');

$pagerBug = new pager(0, 20, 1, 'bug');
r(count($ppmModel->getStaticScanIssuesTest(42, $pagerBug, 81, 'bug'))) && p() && e('1');
