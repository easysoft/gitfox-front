#!/usr/bin/env php
<?php
include dirname(__FILE__, 5) . '/test/lib/init.php';
include dirname(__FILE__, 2) . '/lib/model.class.php';

/**

title=测试 codescanModel->getManualIssues();
timeout=0
cid=0

- 分页查询返回第一页 1 条 @1
- 分页 total 正确 @4
- 按状态过滤人工问题 @2
- 非人工问题不计入 @5
- 正文保留数据库中的原始内容 @&lt;p&gt;人工问题正文&lt;/p&gt;
- 非法状态返回空 @0
- 合并请求字段存在时优先按合并请求过滤 @4
- 合并请求为 0 时回退按代码库过滤 @1
- 已转Bug 的问题按缺陷关联过滤 @1

*/

global $app, $tester;
$app->loadClass('pager', true);

$tester->dao->query('TRUNCATE TABLE ' . TABLE_SCANISSUE);
$hasPPMID   = (bool)$tester->dao->query('SHOW COLUMNS FROM ' . TABLE_SCANISSUE . " LIKE 'ppmID'")->fetch();
$hasContent = (bool)$tester->dao->query('SHOW COLUMNS FROM ' . TABLE_SCANISSUE . " LIKE 'content'")->fetch();

$issues = array(
    array('id' => 1, 'issueKey' => 'manual-1', 'repoID' => 24, 'scanMethod' => 'manual', 'status' => 'wait',   'deleted' => 0),
    array('id' => 2, 'issueKey' => 'manual-2', 'repoID' => 24, 'scanMethod' => 'manual', 'status' => 'solved', 'deleted' => 0),
    array('id' => 3, 'issueKey' => 'manual-3', 'repoID' => 24, 'scanMethod' => 'manual', 'status' => 'closed', 'deleted' => 0),
    array('id' => 4, 'issueKey' => 'manual-4', 'repoID' => 24, 'scanMethod' => 'manual', 'status' => 'wait',   'deleted' => 0),
    array('id' => 5, 'issueKey' => 'manual-5', 'repoID' => 24, 'scanMethod' => 'check',  'status' => 'wait',   'deleted' => 0),
    array('id' => 6, 'issueKey' => 'manual-6', 'repoID' => 99, 'scanMethod' => 'manual', 'status' => 'closed', 'deleted' => 0)
);
if($hasPPMID)
{
    $ppmIDs = array(1 => 5, 2 => 5, 3 => 5, 4 => 0, 5 => 8, 6 => 5);
    foreach($issues as $index => $issue) $issues[$index]['ppmID'] = $ppmIDs[$issue['id']];
}
if($hasContent) $issues[0]['content'] = '&lt;p&gt;人工问题正文&lt;/p&gt;';
foreach($issues as $issue) $tester->dao->insert(TABLE_SCANISSUE)->data((object)$issue)->exec();

$tester->dao->insert(TABLE_BUG)->data((object)array('id' => 1, 'product' => 1, 'issueKey' => '2', 'deleted' => 0))->exec();

su('admin');
$test = new codescanModelTest();

$pager = new pager(0, 1, 1, 'manual');
$all = $test->getManualIssuesTest(0, 24, $pager, 'all');
r(count($all)) && p() && e('1');
r($pager->recTotal) && p() && e('4');

$repoList = $test->getManualIssuesTest(0, 24, null, 'wait');
r(count($repoList)) && p() && e('2');

r($test->getManualIssuesTotalTest(0, 0)) && p() && e('5');
if($hasContent)
{
    $contentIssue = zget($repoList, 1, new stdclass());
    r($contentIssue->content) && p() && e('&lt;p&gt;人工问题正文&lt;/p&gt;');
}

$unknown = $test->getManualIssuesTest(0, 24, null, 'unknown');
r(count($unknown)) && p() && e('0');

if($hasPPMID)
{
    r($test->getManualIssuesTotalTest(5, 24))                && p() && e('4');
    r(count($test->getManualIssuesTest(0, 99, null, 'all'))) && p() && e('1');
}

r(count($test->getManualIssuesTest(0, 24, null, 'converted'))) && p() && e('1');
