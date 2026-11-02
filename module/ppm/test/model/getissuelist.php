#!/usr/bin/env php
<?php

/**

title=测试 ppmModel::getIssueList();
timeout=0
cid=0

- 执行$scanMethods @check
- 执行ppmModel模块的getIssueListTest方法，参数是42, 81, 'static', 'solved', $solvedPager  @1
- 执行$manualLineRange @10-10
- 执行$aiIssues @0
- 执行$pager->recTotal @1
- 执行ppmModel模块的getIssueListTest方法，参数是42, 81, 'unknown', 'wait', $invalidPager  @0
- 执行ppmModel模块的getIssueListTest方法，参数是42, 81, 'scan', 'all', $scanPager  @0
- 执行$emptyPager->recTotal @0

*/

include dirname(__FILE__, 5) . '/test/lib/init.php';
include dirname(__FILE__, 2) . '/lib/model.class.php';

global $app;
$app->loadClass('pager', true);
$app->rawModule = 'ppm';
$app->rawMethod = 'view';
$app->setMethodName('view');

zendata('ops_scan_issues')->gen(0);

$scanIssue = zendata('ops_scan_issues');
$scanIssue->id->range('1-4');
$scanIssue->issueKey->range('ppm-list-check,ppm-list-smell,ppm-list-ai,ppm-list-manual');
$scanIssue->repoID->range('42{4}');
$scanIssue->ppmID->range('81{4}');
$scanIssue->scanMethod->range('check,smell,ai,manual');
$scanIssue->status->range('wait,solved,wait,wait');
$scanIssue->path->range('/src/a.php,/src/b.php,/src/c.php,/src/d.php');
$scanIssue->startLine->range('10,20,30,10');
$scanIssue->endLine->range('12,22,32,9');
$scanIssue->content->range('check,smell,ai,&lt;p&gt;人工问题&lt;/p&gt;');
$scanIssue->deleted->range('0{4}');
$scanIssue->gen(4);

su('admin');

$ppmModel = new ppmModelTest();

$pager = new pager(0, 20, 1, 'static');
$scanIssues = $ppmModel->getIssueListTest(42, 81, 'static', 'wait', $pager);
$scanMethods = is_array($scanIssues) ? implode(',', array_column($scanIssues, 'scanMethod')) : '';
r($scanMethods) && p() && e('check');

$solvedPager = new pager(0, 20, 1, 'solved');
r(count((array)$ppmModel->getIssueListTest(42, 81, 'static', 'solved', $solvedPager))) && p() && e('1');

$manualPager = new pager(0, 20, 1, 'manual');
$manualIssues = $ppmModel->getIssueListTest(42, 81, 'manual', 'all', $manualPager);
$manualLineRange = is_array($manualIssues) && isset($manualIssues[4]) ? str_replace('#', '', $manualIssues[4]->lineRange) : '';
r($manualLineRange) && p() && e('10-10');

$aiPager = new pager(0, 20, 1, 'ai');
$aiIssues = $ppmModel->getIssueListTest(42, 81, 'ai', 'wait', $aiPager);
r(count((array)$aiIssues)) && p() && e('0');

r($pager->recTotal) && p() && e('1');

$invalidPager = new pager(0, 20, 1, 'invalid');
r(count((array)$ppmModel->getIssueListTest(42, 81, 'unknown', 'wait', $invalidPager))) && p() && e('0');

$scanPager = new pager(0, 20, 1, 'scan');
r(count((array)$ppmModel->getIssueListTest(42, 81, 'scan', 'all', $scanPager))) && p() && e('0');

$emptyPager = new pager(0, 20, 1, 'empty');
$ppmModel->getIssueListTest(42, 81, 'static', 'closed', $emptyPager);
r($emptyPager->recTotal) && p() && e('0');
