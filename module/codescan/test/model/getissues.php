#!/usr/bin/env php
<?php
include dirname(__FILE__, 5) . '/test/lib/init.php';
include dirname(__FILE__, 2) . '/lib/model.class.php';

/**

title=测试 codescanModel->getIssues();
timeout=0
cid=0

- 执行test模块的getIssuesTest方法  @6
- 执行test模块的getIssuesTest方法，参数是24  @5
- 执行test模块的getIssuesTest方法，参数是0, 5  @5
- 执行test模块的getIssuesTest方法，参数是24, 5  @4
- 执行test模块的getIssuesTest方法，参数是99  @1
- 执行test模块的getIssuesTest方法，参数是0, 0  @7,6,5,3,2,1

- 执行test模块的getIssuesTest方法，参数是24, 5, 'static', 'all'  @2
- 执行test模块的getIssuesTest方法，参数是24, 5, 'ai', 'wait'  @1
- 执行test模块的getIssuesTest方法，参数是24, 5, 'manual', 'wait'  @1
- 执行test模块的getIssuesTest方法，参数是24, 5, 'static', 'wait'  @1
- 执行$firstPage) . ':' . $pager->recTotal @1:2
- 执行test模块的getIssuesTest方法，参数是24, 5, 'manual', 'converted'  @1
- 执行test模块的getIssuesTest方法，参数是24, 5, 'static', 'bug'  @1
- 执行test模块的getIssuesTest方法，参数是24, 5, 'scan', 'all'  @0
- 执行$staticIssues[5], 'bugID', 0 @101
- 执行$staticIssues[6], 'bugID', 0 @0

*/

global $app, $tester;
$app->loadClass('pager', true);

$tester->dao->query('TRUNCATE TABLE ' . TABLE_SCANISSUE);

$issues = array(
    array('id' => 1, 'issueKey' => 'issue-1', 'title' => '问题1', 'path' => '/src/a.php', 'repoID' => 24, 'ppmID' => 5, 'status' => 'wait',   'scanMethod' => 'manual', 'deleted' => 0),
    array('id' => 2, 'issueKey' => 'issue-2', 'title' => '问题2', 'path' => '/src/b.php', 'repoID' => 24, 'ppmID' => 0, 'status' => 'wait',   'scanMethod' => 'manual', 'deleted' => 0),
    array('id' => 3, 'issueKey' => 'issue-3', 'title' => '问题3', 'path' => '/src/c.php', 'repoID' => 99, 'ppmID' => 5, 'status' => 'wait',   'scanMethod' => 'manual', 'deleted' => 0),
    array('id' => 4, 'issueKey' => 'issue-4', 'title' => '问题4', 'path' => '/src/d.php', 'repoID' => 24, 'ppmID' => 5, 'status' => 'wait',   'scanMethod' => 'manual', 'deleted' => 1),
    array('id' => 5, 'issueKey' => 'issue-5', 'title' => '问题5', 'path' => '/src/e.php', 'repoID' => 24, 'ppmID' => 5, 'status' => 'wait',   'scanMethod' => 'check',  'deleted' => 0),
    array('id' => 6, 'issueKey' => 'issue-6', 'title' => '问题6', 'path' => '/src/f.php', 'repoID' => 24, 'ppmID' => 5, 'status' => 'solved', 'scanMethod' => 'smell',  'deleted' => 0),
    array('id' => 7, 'issueKey' => 'issue-7', 'title' => '问题7', 'path' => '/src/g.php', 'repoID' => 24, 'ppmID' => 5, 'status' => 'wait',   'scanMethod' => 'ai',     'deleted' => 0)
);
foreach($issues as $issue) $tester->dao->insert(TABLE_SCANISSUE)->data((object)$issue)->exec();

$tester->dao->delete()->from(TABLE_BUG)->where('issueKey')->in('1,5,6')->exec();
$bugs = array(
    array('id' => 100, 'issueKey' => '1', 'deleted' => 0),
    array('id' => 101, 'issueKey' => '5', 'deleted' => 0),
    array('id' => 102, 'issueKey' => '6', 'deleted' => 1)
);
foreach($bugs as $bug) $tester->dao->insert(TABLE_BUG)->data((object)$bug)->exec();

su('admin');
$test = new codescanModelTest();

r(count($test->getIssuesTest()))                    && p() && e('6');
r(count($test->getIssuesTest(24)))                  && p() && e('5');
r(count($test->getIssuesTest(0, 5)))                && p() && e('5');
r(count($test->getIssuesTest(24, 5)))               && p() && e('4');
r(count($test->getIssuesTest(99)))                  && p() && e('1');
r(implode(',', array_keys($test->getIssuesTest(0, 0)))) && p() && e('7,6,5,3,2,1');
r(count($test->getIssuesTest(24, 5, 'static', 'all'))) && p() && e('2');
r(count($test->getIssuesTest(24, 5, 'ai', 'wait')))    && p() && e('1');
r(count($test->getIssuesTest(24, 5, 'manual', 'wait'))) && p() && e('1');
r(count($test->getIssuesTest(24, 5, 'static', 'wait'))) && p() && e('1');

$pager = new pager(0, 1, 1, 'type');
$firstPage = $test->getIssuesTest(24, 5, 'static', 'all', $pager);
r(count($firstPage) . ':' . $pager->recTotal) && p() && e('1:2');

r(count($test->getIssuesTest(24, 5, 'manual', 'converted'))) && p() && e('1');
r(count($test->getIssuesTest(24, 5, 'static', 'bug')))       && p() && e('1');
r(count($test->getIssuesTest(24, 5, 'scan', 'all')))         && p() && e('0');

$staticIssues = $test->getIssuesTest(24, 5, 'static', 'all');
r(zget($staticIssues[5], 'bugID', 0)) && p() && e('101');
r(zget($staticIssues[6], 'bugID', 0)) && p() && e('0');
