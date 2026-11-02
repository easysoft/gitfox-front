#!/usr/bin/env php
<?php

/**

title=测试 ppmModel::mapIssueStatus();
timeout=0
cid=0

- 执行ppmModel模块的mapIssueStatusTest方法，参数是'bug', 'wait'  @active
- 执行ppmModel模块的mapIssueStatusTest方法，参数是'bug', 'solved'  @resolved
- 执行ppmModel模块的mapIssueStatusTest方法，参数是'bug', 'closed'  @closed
- 执行ppmModel模块的mapIssueStatusTest方法，参数是'bug', 'ignore') === '' ? 1 : 0  @1
- 执行ppmModel模块的mapIssueStatusTest方法，参数是'static', 'wait'  @wait
- 执行ppmModel模块的mapIssueStatusTest方法，参数是'ai', 'solved') === '' ? 1 : 0  @1
- 执行ppmModel模块的mapIssueStatusTest方法，参数是'unknown', 'wait') === '' ? 1 : 0  @1
- 执行ppmModel模块的mapIssueStatusTest方法，参数是'bug', '') === '' ? 1 : 0  @1

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

r($ppmModel->mapIssueStatusTest('bug', 'wait')) && p() && e('active');
r($ppmModel->mapIssueStatusTest('bug', 'solved')) && p() && e('resolved');
r($ppmModel->mapIssueStatusTest('bug', 'closed')) && p() && e('closed');
r($ppmModel->mapIssueStatusTest('bug', 'ignore') === '' ? 1 : 0) && p() && e('1');
r($ppmModel->mapIssueStatusTest('static', 'wait')) && p() && e('wait');
r($ppmModel->mapIssueStatusTest('ai', 'solved') === '' ? 1 : 0) && p() && e('1');
r($ppmModel->mapIssueStatusTest('unknown', 'wait') === '' ? 1 : 0) && p() && e('1');
r($ppmModel->mapIssueStatusTest('bug', '') === '' ? 1 : 0) && p() && e('1');
