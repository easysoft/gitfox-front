#!/usr/bin/env php
<?php

/**

title=测试 ppmModel::getStaticScanIssueTotal();
timeout=0
cid=0

- 执行ppmModel模块的getStaticScanIssueTotalTest方法，参数是42, 81  @0
- 执行ppmModel模块的getStaticScanIssueTotalTest方法，参数是42, 81)) ? 1 : 0  @1
- 执行ppmModel模块的getStaticScanIssueTotalTest方法，参数是42, 0  @0
- 执行ppmModel模块的getStaticScanIssueTotalTest方法，参数是0, 0  @0
- 执行ppmModel模块的getStaticScanIssueTotalTest方法，参数是0, 81)) ? 1 : 0  @1
- 执行ppmModel模块的getStaticScanIssueTotalTest方法，参数是7, 81  @0

*/

include dirname(__FILE__, 5) . '/test/lib/init.php';
include dirname(__FILE__, 2) . '/lib/model.class.php';

global $app;
$app->rawModule = 'ppm';
$app->rawMethod = 'browse';
$app->setMethodName('browse');

zenData('entry')->loadYaml('entry', false, 2)->gen(1);
zenData('user')->gen(3);
zenData('product')->gen(1);

$repo = zenData('ops_repo')->loadYaml('ops_repo', false, 2);
$repo->id->range('42');
$repo->product->range('1');
$repo->name->range('ppm-repo-42');
$repo->gen(1);

$ppm = zenData('ops_ppm')->loadYaml('ops_ppm', false, 2);
$ppm->id->range('81');
$ppm->title->range('API PPM 81');
$ppm->repoID->range('42');
$ppm->sourceRepoID->range('42');
$ppm->targetRepoID->range('42');
$ppm->targetBranch->range('main');
$ppm->status->range('opened');
$ppm->createdBy->range('admin');
$ppm->gen(1);

su('admin');

$ppmModel = new ppmModelTest();

r($ppmModel->getStaticScanIssueTotalTest(42, 81)) && p() && e('0');
r(is_int($ppmModel->getStaticScanIssueTotalTest(42, 81)) ? 1 : 0) && p() && e('1');
r($ppmModel->getStaticScanIssueTotalTest(42, 0)) && p() && e('0');
r($ppmModel->getStaticScanIssueTotalTest(0, 0)) && p() && e('0');
r(is_int($ppmModel->getStaticScanIssueTotalTest(0, 81)) ? 1 : 0) && p() && e('1');
r($ppmModel->getStaticScanIssueTotalTest(7, 81)) && p() && e('0');