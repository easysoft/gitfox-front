#!/usr/bin/env php
<?php
include dirname(__FILE__, 5) . '/test/lib/init.php';
include dirname(__FILE__, 2) . '/lib/model.class.php';

/**

title=测试 ppmModel::getManualIssues();
timeout=0
cid=0

- 分页查询返回第一页 1 条 @1
- 分页总量正确 @4
- 按状态查询人工审查问题 @2
- 合并请求字段存在时优先按合并请求查询 @4
- 按代码库查询人工审查问题（非人工问题不计入） @4
- 代码库没有人工审查问题时返回空 @0

*/

global $app, $tester;
$app->loadClass('pager', true);

$tester->dao->query('TRUNCATE TABLE ' . TABLE_SCANISSUE);

$issues = array(
    array('id' => 1, 'issueKey' => 'manual-1', 'repoID' => 24, 'ppmID' => 5, 'scanMethod' => 'manual', 'status' => 'wait',   'deleted' => 0),
    array('id' => 2, 'issueKey' => 'manual-2', 'repoID' => 24, 'ppmID' => 5, 'scanMethod' => 'manual', 'status' => 'solved', 'deleted' => 0),
    array('id' => 3, 'issueKey' => 'manual-3', 'repoID' => 24, 'ppmID' => 5, 'scanMethod' => 'manual', 'status' => 'closed', 'deleted' => 0),
    array('id' => 4, 'issueKey' => 'manual-4', 'repoID' => 24, 'ppmID' => 0, 'scanMethod' => 'manual', 'status' => 'wait',   'deleted' => 0),
    array('id' => 5, 'issueKey' => 'check-1',  'repoID' => 24, 'ppmID' => 8, 'scanMethod' => 'check',  'status' => 'wait',   'deleted' => 0),
    array('id' => 6, 'issueKey' => 'manual-6', 'repoID' => 99, 'ppmID' => 5, 'scanMethod' => 'manual', 'status' => 'closed', 'deleted' => 0)
);
foreach($issues as $issue) $tester->dao->insert(TABLE_SCANISSUE)->data((object)$issue)->exec();

su('admin');
$test = new ppmModelTest();

$pager = new pager(0, 1, 1, 'manual');
$all   = $test->getManualIssuesTest(0, 24, $pager, 'all');
r(count($all))       && p() && e('1');
r($pager->recTotal)  && p() && e('4');

r(count($test->getManualIssuesTest(0, 24, null, 'wait')))  && p() && e('2');
r(count($test->getManualIssuesTest(5, 24, null, 'all')))   && p() && e('4');
r(count($test->getManualIssuesTest(0, 24, null, 'all')))   && p() && e('4');
r(count($test->getManualIssuesTest(0, 100, null, 'all')))  && p() && e('0');
