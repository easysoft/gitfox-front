#!/usr/bin/env php
<?php
include dirname(__FILE__, 5) . '/test/lib/init.php';
include dirname(__FILE__, 2) . '/lib/model.class.php';

/**

title=测试 codescanModel->getManualIssues();
timeout=0
cid=0

- 按代码库过滤全部人工问题 @4
- 按状态过滤人工问题 @2
- 分页 total 正确 @4
- 非人工问题不计入 @5
- 合并请求字段存在时按合并请求过滤 @3

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

if($hasPPMID) r($test->getManualIssuesTotalTest(5, 24)) && p() && e('3');
