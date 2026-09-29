#!/usr/bin/env php
<?php
include dirname(__FILE__, 5) . '/test/lib/init.php';
include dirname(__FILE__, 2) . '/lib/model.class.php';

/**

title=测试 codescanModel->getIssueByID();
timeout=0
cid=0

- 按主键获取问题详情 @1
- 返回问题的文件路径 @/src/a.php
- 返回问题的起始行 @10
- 按主键获取第二个问题 @2
- 不限制扫描方式，扫描任务产生的问题也能取到 @check
- 只按主键查询，已删除的问题同样返回 @3

*/

global $tester;

$tester->dao->query('TRUNCATE TABLE ' . TABLE_SCANISSUE);

$issues = array(
    array('id' => 1, 'issueKey' => 'issue-1', 'title' => '人工审查问题', 'path' => '/src/a.php', 'startLine' => 10, 'repoID' => 24, 'status' => 'wait', 'scanMethod' => 'manual', 'deleted' => 0),
    array('id' => 2, 'issueKey' => 'issue-2', 'title' => '静态扫描问题', 'path' => '/src/b.php', 'startLine' => 3,  'repoID' => 24, 'status' => 'wait', 'scanMethod' => 'check',  'deleted' => 0),
    array('id' => 3, 'issueKey' => 'issue-3', 'title' => '已删除的问题', 'path' => '/src/c.php', 'startLine' => 0,  'repoID' => 24, 'status' => 'wait', 'scanMethod' => 'manual', 'deleted' => 1)
);
foreach($issues as $issue) $tester->dao->insert(TABLE_SCANISSUE)->data((object)$issue)->exec();

su('admin');
$test = new codescanModelTest();

$issue = $test->getIssueByIDTest(1);
r($issue->id)        && p() && e('1');
r($issue->path)      && p() && e('/src/a.php');
r($issue->startLine) && p() && e('10');

r($test->getIssueByIDTest(2)->id)         && p() && e('2');
r($test->getIssueByIDTest(2)->scanMethod) && p() && e('check');
r($test->getIssueByIDTest(3)->id)         && p() && e('3');
