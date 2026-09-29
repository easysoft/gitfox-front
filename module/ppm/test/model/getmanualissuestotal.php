#!/usr/bin/env php
<?php
include dirname(__FILE__, 5) . '/test/lib/init.php';
include dirname(__FILE__, 2) . '/lib/model.class.php';

/**

title=测试 ppmModel::getManualIssuesTotal();
timeout=0
cid=0

- 按代码库统计人工审查问题总数 @4
- 代码库没有人工审查问题时为 0 @0
- 非人工问题不计入总数 @5
- 合并请求字段存在时优先按合并请求统计 @4
- 合并请求下没有人工审查问题时为 0 @0

*/

global $tester;

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

r($test->getManualIssuesTotalTest(0, 24))  && p() && e('4');
r($test->getManualIssuesTotalTest(0, 100)) && p() && e('0');
r($test->getManualIssuesTotalTest(0, 0))   && p() && e('5');

r($test->getManualIssuesTotalTest(5, 24))  && p() && e('4');
r($test->getManualIssuesTotalTest(8, 24))  && p() && e('0');
