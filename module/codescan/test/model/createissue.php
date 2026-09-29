#!/usr/bin/env php
<?php
include dirname(__FILE__, 5) . '/test/lib/init.php';
include dirname(__FILE__, 2) . '/lib/model.class.php';

/**

title=测试 codescanModel->createIssue();
timeout=0
cid=0

- 新增问题返回自增主键 @1
- 新增的问题可以按主键查到 @人工审查问题
- 新增的问题扫描方式是人工审查 @manual
- 新增的问题状态是待处理 @wait
- 新增的问题起始行正确 @10
- 连续新增返回不同的主键 @2

*/

global $tester;

$tester->dao->query('TRUNCATE TABLE ' . TABLE_SCANISSUE);

su('admin');
$test = new codescanModelTest();

$formData = new stdclass();
$formData->issueKey   = 'create-1';
$formData->title      = '人工审查问题';
$formData->path       = '/src/a.php';
$formData->startLine  = 10;
$formData->endLine    = 12;
$formData->repoID     = 24;
$formData->status     = 'wait';
$formData->scanMethod = 'manual';
$formData->deleted    = 0;

$issueID = $test->createIssueTest($formData);
r($issueID) && p() && e('1');

$issue = $test->getIssueByIDTest($issueID);
r($issue->title)      && p() && e('人工审查问题');
r($issue->scanMethod) && p() && e('manual');
r($issue->status)     && p() && e('wait');
r($issue->startLine)  && p() && e('10');

$formData->issueKey = 'create-2';
r($test->createIssueTest($formData)) && p() && e('2');
