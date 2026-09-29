#!/usr/bin/env php
<?php
include dirname(__FILE__, 5) . '/test/lib/init.php';
include dirname(__FILE__, 2) . '/lib/model.class.php';

/**

title=测试 codescanModel->getIssues();
timeout=0
cid=0

- 不传条件返回全部未删除问题 @3
- 按代码库过滤问题 @2
- 按合并请求过滤问题 @2
- 同时按代码库和合并请求过滤问题 @1
- 只按代码库过滤出另一个代码库的问题 @1
- 结果按主键倒序返回 @3,2,1

*/

global $tester;

$tester->dao->query('TRUNCATE TABLE ' . TABLE_SCANISSUE);

$issues = array(
    array('id' => 1, 'issueKey' => 'issue-1', 'title' => '问题1', 'path' => '/src/a.php', 'repoID' => 24, 'ppmID' => 5, 'status' => 'wait', 'scanMethod' => 'manual', 'deleted' => 0),
    array('id' => 2, 'issueKey' => 'issue-2', 'title' => '问题2', 'path' => '/src/b.php', 'repoID' => 24, 'ppmID' => 0, 'status' => 'wait', 'scanMethod' => 'manual', 'deleted' => 0),
    array('id' => 3, 'issueKey' => 'issue-3', 'title' => '问题3', 'path' => '/src/c.php', 'repoID' => 99, 'ppmID' => 5, 'status' => 'wait', 'scanMethod' => 'manual', 'deleted' => 0),
    array('id' => 4, 'issueKey' => 'issue-4', 'title' => '已删除的问题', 'path' => '/src/d.php', 'repoID' => 24, 'ppmID' => 5, 'status' => 'wait', 'scanMethod' => 'manual', 'deleted' => 1)
);
foreach($issues as $issue) $tester->dao->insert(TABLE_SCANISSUE)->data((object)$issue)->exec();

su('admin');
$test = new codescanModelTest();

r(count($test->getIssuesTest()))                    && p() && e('3');
r(count($test->getIssuesTest(24)))                  && p() && e('2');
r(count($test->getIssuesTest(0, 5)))                && p() && e('2');
r(count($test->getIssuesTest(24, 5)))               && p() && e('1');
r(count($test->getIssuesTest(99)))                  && p() && e('1');
r(implode(',', array_keys($test->getIssuesTest()))) && p() && e('3,2,1');
