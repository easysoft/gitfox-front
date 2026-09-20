#!/usr/bin/env php
<?php

/**

title=测试 ppmModel::mapIssueStatus();
timeout=0
cid=0

- 执行ppmModel模块的mapIssueStatusTest方法，参数是'static', 'wait'  @active
- 执行ppmModel模块的mapIssueStatusTest方法，参数是'static', 'solved'  @resolved
- 执行ppmModel模块的mapIssueStatusTest方法，参数是'static', 'closed'  @closed
- 执行ppmModel模块的mapIssueStatusTest方法，参数是'static', 'ignore') === '' ? 1 : 0  @1
- 执行ppmModel模块的mapIssueStatusTest方法，参数是'scan', 'wait'  @wait
- 执行ppmModel模块的mapIssueStatusTest方法，参数是'ai', 'solved'  @solved
- 执行ppmModel模块的mapIssueStatusTest方法，参数是'unknown', 'wait') === '' ? 1 : 0  @1
- 执行ppmModel模块的mapIssueStatusTest方法，参数是'static', '') === '' ? 1 : 0  @1

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

r($ppmModel->mapIssueStatusTest('static', 'wait')) && p() && e('active');
r($ppmModel->mapIssueStatusTest('static', 'solved')) && p() && e('resolved');
r($ppmModel->mapIssueStatusTest('static', 'closed')) && p() && e('closed');
r($ppmModel->mapIssueStatusTest('static', 'ignore') === '' ? 1 : 0) && p() && e('1');
r($ppmModel->mapIssueStatusTest('scan', 'wait')) && p() && e('wait');
r($ppmModel->mapIssueStatusTest('ai', 'solved')) && p() && e('solved');
r($ppmModel->mapIssueStatusTest('unknown', 'wait') === '' ? 1 : 0) && p() && e('1');
r($ppmModel->mapIssueStatusTest('static', '') === '' ? 1 : 0) && p() && e('1');