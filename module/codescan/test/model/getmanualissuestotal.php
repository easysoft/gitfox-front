#!/usr/bin/env php
<?php
include dirname(__FILE__, 5) . '/test/lib/init.php';
include dirname(__FILE__, 2) . '/lib/model.class.php';

/**

title=测试 codescanModel->getManualIssuesTotal();
timeout=0
cid=0

- 按代码库统计人工问题总数 @4
- 代码库没有人工问题时为 0 @0
- 非人工问题不计入 @5
- 全部代码库统计人工问题总数 @5
- 合并请求字段存在时按合并请求统计 @3

*/

global $tester;

$tester->dao->query('TRUNCATE TABLE ' . TABLE_SCANISSUE);
$hasPPMID = (bool)$tester->dao->query('SHOW COLUMNS FROM ' . TABLE_SCANISSUE . " LIKE 'ppmID'")->fetch();

$issues = array(
    array('id' => 1, 'issueKey' => 'total-1', 'repoID' => 24, 'scanMethod' => 'manual', 'status' => 'wait',   'deleted' => 0),
    array('id' => 2, 'issueKey' => 'total-2', 'repoID' => 24, 'scanMethod' => 'manual', 'status' => 'solved', 'deleted' => 0),
    array('id' => 3, 'issueKey' => 'total-3', 'repoID' => 24, 'scanMethod' => 'manual', 'status' => 'closed', 'deleted' => 0),
    array('id' => 4, 'issueKey' => 'total-4', 'repoID' => 24, 'scanMethod' => 'manual', 'status' => 'wait',   'deleted' => 0),
    array('id' => 5, 'issueKey' => 'total-5', 'repoID' => 24, 'scanMethod' => 'check',  'status' => 'wait',   'deleted' => 0),
    array('id' => 6, 'issueKey' => 'total-6', 'repoID' => 99, 'scanMethod' => 'manual', 'status' => 'closed', 'deleted' => 0)
);
if($hasPPMID)
{
    $ppmIDs = array(1 => 5, 2 => 5, 3 => 5, 4 => 0, 5 => 8, 6 => 5);
    foreach($issues as $index => $issue) $issues[$index]['ppmID'] = $ppmIDs[$issue['id']];
}
foreach($issues as $issue) $tester->dao->insert(TABLE_SCANISSUE)->data((object)$issue)->exec();

su('admin');
$test = new codescanModelTest();

r($test->getManualIssuesTotalTest(0, 24)) && p() && e('4');
r($test->getManualIssuesTotalTest(0, 100)) && p() && e('0');
r($test->getManualIssuesTotalTest(0, 0)) && p() && e('5');

if($hasPPMID)
{
    r($test->getManualIssuesTotalTest(5, 24)) && p() && e('3');
    r($test->getManualIssuesTotalTest(8, 24)) && p() && e('0');
}
