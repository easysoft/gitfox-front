#!/usr/bin/env php
<?php

/**

title=测试 ppmModel::getIssueTotal();
timeout=0
cid=0

- 执行ppmModel模块的getIssueTotalTest方法，参数是81  @5
- 执行ppmModel模块的getIssueTotalTest方法，参数是82  @1
- 执行ppmModel模块的getIssueTotalTest方法，参数是999  @0
- 执行ppmModel模块的getIssueTotalTest方法  @6
- 执行ppmModel模块的getIssueTotalTest方法，参数是81)) ? 1 : 0  @1

*/

include dirname(__FILE__, 5) . '/test/lib/init.php';
include dirname(__FILE__, 2) . '/lib/model.class.php';

global $app;
$app->rawModule = 'ppm';
$app->rawMethod = 'view';
$app->setMethodName('view');

zendata('ops_scan_issues')->gen(0);

$scanIssue = zendata('ops_scan_issues');
$scanIssue->id->range('1-7');
$scanIssue->issueKey->range('ppm-total-1,ppm-total-2,ppm-total-3,ppm-total-4,ppm-total-5,ppm-total-6,ppm-total-7');
$scanIssue->repoID->range('42,42,42,42,99,42,42');
$scanIssue->ppmID->range('81,81,81,81,81,82,81');
$scanIssue->scanMethod->range('check,smell,ai,manual,check,check,manual');
$scanIssue->status->range('wait,solved,wait,closed,wait,wait,wait');
$scanIssue->deleted->range('0,0,0,0,0,0,1');
$scanIssue->gen(7);

su('admin');
$ppmModel = new ppmModelTest();

r($ppmModel->getIssueTotalTest(81)) && p() && e('5');
r($ppmModel->getIssueTotalTest(82)) && p() && e('1');
r($ppmModel->getIssueTotalTest(999)) && p() && e('0');
r($ppmModel->getIssueTotalTest(0)) && p() && e('6');
r(is_int($ppmModel->getIssueTotalTest(81)) ? 1 : 0) && p() && e('1');
