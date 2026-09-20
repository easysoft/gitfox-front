#!/usr/bin/env php
<?php

/**

title=测试 ppmModel::getIssueBadgeCount();
timeout=0
cid=0

- 执行ppmModel模块的getIssueBadgeCountTest方法，参数是'ai', 42, 81  @4
- 执行ppmModel模块的getIssueBadgeCountTest方法，参数是'scan', 42, 81  @0
- 执行ppmModel模块的getIssueBadgeCountTest方法，参数是'ai', 999, 81  @0
- 执行ppmModel模块的getIssueBadgeCountTest方法，参数是'ai', 42, 999  @0
- 执行ppmModel模块的getIssueBadgeCountTest方法，参数是'ai', 42, 0  @4

*/

include dirname(__FILE__, 5) . '/test/lib/init.php';
include dirname(__FILE__, 2) . '/lib/model.class.php';

global $app;
$app->rawModule = 'ppm';
$app->rawMethod = 'browse';
$app->setMethodName('browse');

zenData('entry')->loadYaml('entry', false, 2)->gen(1);
zenData('user')->gen(3);

/* 6 条 AI 问题：四种有标签状态各一条，另加 todo / solving 各一条（不应计入徽标）。 */
zendata('ops_scan_issues')->gen(0);

$scanIssue = zendata('ops_scan_issues');
$scanIssue->id->range('1-6');
$scanIssue->issueKey->range('badge-key1,badge-key2,badge-key3,badge-key4,badge-key5,badge-key6');
$scanIssue->repoID->range('42');
$scanIssue->ppmID->range('81');
$scanIssue->scanMethod->range('ai');
$scanIssue->status->range('wait,solved,ignore,closed,todo,solving');
$scanIssue->deleted->range('0{6}');
$scanIssue->gen(6);

su('admin');

$ppmModel = new ppmModelTest();

r($ppmModel->getIssueBadgeCountTest('ai', 42, 81)) && p() && e('4');
r($ppmModel->getIssueBadgeCountTest('scan', 42, 81)) && p() && e('0');
r($ppmModel->getIssueBadgeCountTest('ai', 999, 81)) && p() && e('0');
r($ppmModel->getIssueBadgeCountTest('ai', 42, 999)) && p() && e('0');
r($ppmModel->getIssueBadgeCountTest('ai', 42, 0)) && p() && e('4');