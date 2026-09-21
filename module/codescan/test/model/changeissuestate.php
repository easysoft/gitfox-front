#!/usr/bin/env php
<?php
include dirname(__FILE__, 5) . '/test/lib/init.php';
include dirname(__FILE__, 2) . '/lib/model.class.php';
zenData('entry')->loadYaml('entry', false, 2)->gen(1, true, false);
zenData('action')->gen(0);

/**

title=测试 codescanModel->changeIssueState();
timeout=0
cid=0

- 单个问题标记为已解决返回成功  @1
- 批量问题标记为已解决返回成功  @1
- 批量提交为每个问题各写一条操作历史  @3
- 批量的操作历史动作名为 solvedScanIssue（入库转小写）  @solvedscanissue
- 激活回激活态返回成功  @1
- 零号问题提交仍返回成功  @1
- 非法状态提交仍返回成功  @1

*/

su('admin');
$test = new codescanModelTest();
global $tester;

r($test->changeIssueStateTest(1, 'solved', 'fixed', '2026-07-01', 0)) && p() && e('1');
r($test->changeIssueStateTest(array(3, 4, 5), 'solved', 'duplicate', '2026-07-02', 0)) && p() && e('1');

$batchActions = $tester->dao->select('`id`,`action`')->from(TABLE_ACTION)
    ->where('objectType')->eq('codescanissue')
    ->andWhere('objectID')->in('3,4,5')
    ->fetchAll('id');
r(count($batchActions)) && p() && e('3');
r(reset($batchActions)->action) && p() && e('solvedscanissue');

r($test->changeIssueStateTest(6, 'wait')) && p() && e('1');
r($test->changeIssueStateTest(0, 'wait')) && p() && e('1');
r($test->changeIssueStateTest(7, 'fixed')) && p() && e('1');
