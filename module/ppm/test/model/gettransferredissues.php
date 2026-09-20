#!/usr/bin/env php
<?php

/**

title=测试 ppmModel::getTransferredIssues();
timeout=0
cid=0

- 执行ppmModel模块的getTransferredIssuesTest方法，参数是'ai', 42, 81, $pager1  @2
- 执行$pager1->recTotal @2
- 执行ppmModel模块的getTransferredIssuesTest方法，参数是'ai', 42, 81, $pager2), 'id'  @1,2

- 执行ppmModel模块的getTransferredIssuesTest方法，参数是'ai', 999, 81, $pager1  @0
- 执行ppmModel模块的getTransferredIssuesTest方法，参数是'scan', 42, 81, $pager1  @0

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

/* 4 条 AI 问题，其中 id 为 1、2 的两条在缺陷表里有对应记录。 */
zendata('ops_scan_issues')->gen(0);
$scanIssue = zendata('ops_scan_issues');
$scanIssue->id->range('1-4');
$scanIssue->issueKey->range('transfer-key1,transfer-key2,transfer-key3,transfer-key4');
$scanIssue->repoID->range('42');
$scanIssue->ppmID->range('81');
$scanIssue->scanMethod->range('ai');
$scanIssue->status->range('solving,wait,ignore,closed');
$scanIssue->payload->range('payload-a,payload-b,payload-c,payload-d');
$scanIssue->deleted->range('0{4}');
$scanIssue->gen(4);

zendata('bug')->gen(0);
$bug = zendata('bug');
$bug->id->range('1-2');
$bug->issueKey->range('1,2');
$bug->repo->range('42');
$bug->deleted->range('0{2}');
$bug->gen(2);

/* codescanZen 继承自 codescan control，其构造函数会做 gitfox 健康检查，CLI 下会重定向抛异常。
   这里预注 loadTarget 缓存绕开该构造：processIssueData 是实例方法，不依赖构造。 */
helper::importControl('codescan');
require_once $app->getModulePath('', 'codescan') . 'zen.php';
$zenRef = new ReflectionClass('codescanZen');
baseRouter::$loadedTargets['zen'][$app->appName]['codescan'] = $zenRef->newInstanceWithoutConstructor();

su('admin');

$ppmModel = new ppmModelTest();
$pager1   = new pager(0, 20, 1);
$pager2   = new pager(0, 20, 2);

r(count($ppmModel->getTransferredIssuesTest('ai', 42, 81, $pager1))) && p() && e('2');
r($pager1->recTotal) && p() && e('2');
r(implode(',', array_column($ppmModel->getTransferredIssuesTest('ai', 42, 81, $pager2), 'id'))) && p() && e('1,2');
r(count((array)$ppmModel->getTransferredIssuesTest('ai', 999, 81, $pager1))) && p() && e('0');
r(count((array)$ppmModel->getTransferredIssuesTest('scan', 42, 81, $pager1))) && p() && e('0');