#!/usr/bin/env php
<?php

/**

title=测试 ppmModel::getMRCommitsCached();
timeout=0
cid=0

- 执行$cached1) ? 1 : 0 @1
- 执行$cached1 @0
- 执行$cached1 === $cached2 ? 1 : 0 @1
- 执行ppmModel模块的getMRCommitsCachedTest方法，参数是0, 0)) ? 1 : 0  @1
- 执行ppmModel模块的getMRCommitsCachedTest方法，参数是0, 81  @0
- 执行ppmModel模块的getMRCommitsCachedTest方法，参数是42, 0  @0
- 执行ppmModel模块的getMRCommitsCachedTest方法，参数是42, 81  @0

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
$ppm->sourceBranch->range('feature/bbb');
$ppm->targetRepoID->range('42');
$ppm->targetBranch->range('main');
$ppm->mergeSHA->range('4444444444444444444444444444444444444444');
$ppm->status->range('opened');
$ppm->createdBy->range('admin');
$ppm->gen(1);

su('admin');

$ppmModel = new ppmModelTest();

/* 同一实例内重复取同一个 repoID-ppmID 组合，第二次应命中缓存，结果与第一次全等。 */
$cached1 = $ppmModel->getMRCommitsCachedTest(42, 81);
$cached2 = $ppmModel->getMRCommitsCachedTest(42, 81);

r(is_array($cached1) ? 1 : 0) && p() && e('1');
r(count($cached1)) && p() && e('0');
r($cached1 === $cached2 ? 1 : 0) && p() && e('1');
r(is_array($ppmModel->getMRCommitsCachedTest(0, 0)) ? 1 : 0) && p() && e('1');
r(count($ppmModel->getMRCommitsCachedTest(0, 81))) && p() && e('0');
r(count($ppmModel->getMRCommitsCachedTest(42, 0))) && p() && e('0');
r(count($ppmModel->getMRCommitsCachedTest(42, 81))) && p() && e('0');