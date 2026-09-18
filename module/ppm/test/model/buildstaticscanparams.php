#!/usr/bin/env php
<?php

/**

title=测试 ppmModel::buildStaticScanParams();
timeout=0
cid=0

- 执行$params1 @4
- 执行$params1['repoID'] @42
- 执行$params1['scanMethods'] @check,smell

- 执行$params1['page'] . '-' . $params1['pageSize'] @1-20
- 执行$params1['ppmID']) ? 1 : 0 @0
- 执行$params2 @5
- 执行$params2['ppmID'] @81
- 执行$params2['page'] . '-' . $params2['pageSize'] @3-50

*/

include dirname(__FILE__, 5) . '/test/lib/init.php';
include dirname(__FILE__, 2) . '/lib/model.class.php';

global $app;
$app->rawModule = 'ppm';
$app->rawMethod = 'browse';
$app->setMethodName('browse');

zenData('entry')->loadYaml('entry', false, 2)->gen(1);
zenData('user')->gen(3);

su('admin');

$ppmModel = new ppmModelTest();
$params1  = $ppmModel->buildStaticScanParamsTest(42, 1, 20, 0);
$params2  = $ppmModel->buildStaticScanParamsTest(7, 3, 50, 81);

r(count($params1)) && p() && e('4');
r($params1['repoID']) && p() && e('42');
r(implode(',', $params1['scanMethods'])) && p() && e('check,smell');
r($params1['page'] . '-' . $params1['pageSize']) && p() && e('1-20');
r(isset($params1['ppmID']) ? 1 : 0) && p() && e('0');
r(count($params2)) && p() && e('5');
r($params2['ppmID']) && p() && e('81');
r($params2['page'] . '-' . $params2['pageSize']) && p() && e('3-50');