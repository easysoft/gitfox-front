#!/usr/bin/env php
<?php
include dirname(__FILE__, 5) . '/test/lib/init.php';
include dirname(__FILE__, 2) . '/lib/model.class.php';

/**

title=测试 codescanModel->getManualIssueByID();
timeout=0
cid=0

- 按主键获取人工审查问题 @1
- 返回人工审查问题的标题 @人工审查问题
- 已删除的人工审查问题查不到 @1
- 非人工审查问题查不到 @1
- 问题不存在时返回 false @1

*/

global $tester;

$tester->dao->query('TRUNCATE TABLE ' . TABLE_SCANISSUE);

$issues = array(
    array('id' => 1, 'issueKey' => 'manual-1', 'title' => '人工审查问题', 'path' => '/src/a.php', 'startLine' => 10, 'endLine' => 12, 'repoID' => 24, 'status' => 'wait', 'scanMethod' => 'manual', 'deleted' => 0),
    array('id' => 2, 'issueKey' => 'manual-2', 'title' => '已删除的问题', 'path' => '/src/b.php', 'startLine' => 0,  'endLine' => 0,  'repoID' => 24, 'status' => 'wait', 'scanMethod' => 'manual', 'deleted' => 1),
    array('id' => 3, 'issueKey' => 'check-1',  'title' => '静态扫描问题', 'path' => '/src/c.php', 'startLine' => 0,  'endLine' => 0,  'repoID' => 24, 'status' => 'wait', 'scanMethod' => 'check',  'deleted' => 0)
);
foreach($issues as $issue) $tester->dao->insert(TABLE_SCANISSUE)->data((object)$issue)->exec();

su('admin');
$test = new codescanModelTest();

$issue = $test->getManualIssueByIDTest(1);
r($issue->id)    && p() && e('1');
r($issue->title) && p() && e('人工审查问题');

r($test->getManualIssueByIDTest(2) === false)   && p() && e('1');
r($test->getManualIssueByIDTest(3) === false)   && p() && e('1');
r($test->getManualIssueByIDTest(999) === false) && p() && e('1');
