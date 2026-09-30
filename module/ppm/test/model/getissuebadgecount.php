#!/usr/bin/env php
<?php

/**

title=测试 ppmModel::getIssueBadgeCount();
timeout=0
cid=0

- AI 问题徽标只统计有标签的状态 @4
- 静态扫描问题徽标为 0 @0
- 人工审查问题徽标只统计有标签的状态 @3
- AI 问题按不存在的代码库统计为 0 @0
- AI 问题按不存在的评审统计为 0 @0
- 人工审查问题按不存在的代码库统计为 0 @0
- 人工审查问题按不存在的评审统计为 0 @0
- AI 问题不传评审时按代码库统计 @4
- 人工审查问题不传评审时按代码库统计 @3

*/

include dirname(__FILE__, 5) . '/test/lib/init.php';
include dirname(__FILE__, 2) . '/lib/model.class.php';

global $app;
$app->rawModule = 'ppm';
$app->rawMethod = 'browse';
$app->setMethodName('browse');

zenData('entry')->loadYaml('entry', false, 2)->gen(1);
zenData('user')->gen(3);

/* 6 条 AI 问题 + 5 条人工问题：有标签的状态各计一条，todo / ignore 等无标签状态不计入徽标。 */
zendata('ops_scan_issues')->gen(0);

$scanIssue = zendata('ops_scan_issues');
$scanIssue->id->range('1-11');
$scanIssue->issueKey->range('badge-key1,badge-key2,badge-key3,badge-key4,badge-key5,badge-key6,badge-key7,badge-key8,badge-key9,badge-key10,badge-key11');
$scanIssue->repoID->range('42{11}');
$scanIssue->ppmID->range('81{11}');
$scanIssue->scanMethod->range('ai{6},manual{5}');
$scanIssue->status->range('wait,solved,ignore,closed,todo,solving,wait,solved,closed,todo,ignore');
$scanIssue->deleted->range('0{11}');
$scanIssue->gen(11);

su('admin');

$ppmModel = new ppmModelTest();

r($ppmModel->getIssueBadgeCountTest('ai', 42, 81)) && p() && e('4');
r($ppmModel->getIssueBadgeCountTest('scan', 42, 81)) && p() && e('0');
r($ppmModel->getIssueBadgeCountTest('manual', 42, 81)) && p() && e('3');
r($ppmModel->getIssueBadgeCountTest('ai', 999, 81)) && p() && e('0');
r($ppmModel->getIssueBadgeCountTest('ai', 42, 999)) && p() && e('0');
r($ppmModel->getIssueBadgeCountTest('manual', 999, 81)) && p() && e('0');
r($ppmModel->getIssueBadgeCountTest('manual', 42, 999)) && p() && e('0');
r($ppmModel->getIssueBadgeCountTest('ai', 42, 0)) && p() && e('4');
r($ppmModel->getIssueBadgeCountTest('manual', 42, 0)) && p() && e('3');
