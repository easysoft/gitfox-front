#!/usr/bin/env php
<?php

/**

title=测试 ppmModel::getIssueTotal();
timeout=0
cid=0

- 执行ppmModel模块的getIssueTotalTest方法，参数是42, 81, array  @6
- 执行ppmModel模块的getIssueTotalTest方法，参数是42, 81, array  @7
- 执行ppmModel模块的getIssueTotalTest方法，参数是42, 81, array  @4
- 执行ppmModel模块的getIssueTotalTest方法，参数是42, 81, array  @2
- 执行ppmModel模块的getIssueTotalTest方法，参数是42, 81, array  @0
- 执行ppmModel模块的getIssueTotalTest方法，参数是42, 81, array  @0
- 执行ppmModel模块的getIssueTotalTest方法，参数是99, 81, array  @1
- 执行ppmModel模块的getIssueTotalTest方法，参数是42, 81, array  @1

*/

include dirname(__FILE__, 5) . '/test/lib/init.php';
include dirname(__FILE__, 2) . '/lib/model.class.php';

global $app, $config;
$app->rawModule = 'ppm';
$app->rawMethod = 'view';
$app->setMethodName('view');
$app->loadConfig('ppm');

zendata('ops_scan_issues')->gen(0);

$scanIssue = zendata('ops_scan_issues');
$scanIssue->id->range('1-10');
$scanIssue->issueKey->range('ppm-total-1,ppm-total-2,ppm-total-3,ppm-total-4,ppm-total-5,ppm-total-6,ppm-total-7,ppm-total-8,ppm-total-9,ppm-total-10');
$scanIssue->repoID->range('42,42,42,42,42,42,42,99,42,42');
$scanIssue->ppmID->range('81,81,81,81,81,81,81,81,82,81');
$scanIssue->scanMethod->range('check,smell,check,check,manual,manual,ai,check,check,check');
$scanIssue->status->range('wait,solved,ignore,todo,closed,ignore,wait,wait,wait,wait');
$scanIssue->deleted->range('0,0,0,0,0,0,0,0,0,1');
$scanIssue->gen(10);

$config->ppm->issueScanMethods['ai'] = array('ai');

su('admin');
$ppmModel = new ppmModelTest();

r($ppmModel->getIssueTotalTest(42, 81, array('static', 'manual')))       && p() && e('6');
r($ppmModel->getIssueTotalTest(42, 81, array('static', 'manual', 'ai'))) && p() && e('7');
r($ppmModel->getIssueTotalTest(42, 81, array('static')))                 && p() && e('4');
r($ppmModel->getIssueTotalTest(42, 81, array('manual')))                 && p() && e('2');
r($ppmModel->getIssueTotalTest(42, 81, array('unknown')))                && p() && e('0');
r($ppmModel->getIssueTotalTest(42, 81, array()))                         && p() && e('0');
r($ppmModel->getIssueTotalTest(99, 81, array('static')))                 && p() && e('1');
r(is_int($ppmModel->getIssueTotalTest(42, 81, array('static', 'manual'))) ? 1 : 0) && p() && e('1');
