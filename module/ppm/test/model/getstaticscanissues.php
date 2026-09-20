#!/usr/bin/env php
<?php

/**

title=测试 ppmModel::getStaticScanIssues();
timeout=0
cid=0

- 执行ppmModel模块的getStaticScanIssuesTest方法，参数是42, $pager1, 81)) ? 1 : 0  @1
- 执行ppmModel模块的getStaticScanIssuesTest方法，参数是42, $pager1, 81  @0
- 执行$pager1->recTotal @0
- 执行ppmModel模块的getStaticScanIssuesTest方法，参数是42, $pager1, 0)) ? 1 : 0  @1
- 执行ppmModel模块的getStaticScanIssuesTest方法，参数是0, $pager1, 0)) ? 1 : 0  @1
- 执行$pager2->recTotal @0

*/

include dirname(__FILE__, 5) . '/test/lib/init.php';
include dirname(__FILE__, 2) . '/lib/model.class.php';

global $app;
$app->loadClass('pager', true);
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
$ppm->sourceBranch->range('feature/bbb');
$ppm->targetRepoID->range('42');
$ppm->targetBranch->range('main');
$ppm->status->range('opened');
$ppm->createdBy->range('admin');
$ppm->gen(1);

su('admin');

$ppmModel = new ppmModelTest();
$pager1   = new pager(0, 20, 1);
$pager2   = new pager(0, 20, 2);

r(is_array($ppmModel->getStaticScanIssuesTest(42, $pager1, 81)) ? 1 : 0) && p() && e('1');
r(count((array)$ppmModel->getStaticScanIssuesTest(42, $pager1, 81))) && p() && e('0');
r($pager1->recTotal) && p() && e('0');
r(is_array($ppmModel->getStaticScanIssuesTest(42, $pager1, 0)) ? 1 : 0) && p() && e('1');
r(is_array($ppmModel->getStaticScanIssuesTest(0, $pager1, 0)) ? 1 : 0) && p() && e('1');
r($pager2->recTotal) && p() && e('0');