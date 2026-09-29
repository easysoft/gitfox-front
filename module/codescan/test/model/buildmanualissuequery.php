#!/usr/bin/env php
<?php
include dirname(__FILE__, 5) . '/test/lib/init.php';
include dirname(__FILE__, 2) . '/lib/model.class.php';

/**

title=测试 codescanModel->buildManualIssueQuery();
timeout=0
cid=0

- 非法的状态参数返回 false @1
- 合法参数返回查询对象 @1
- 只查询人工审查问题 @1
- 传入代码库时按代码库过滤 @1
- 状态为 wait 时按状态过滤 @1
- 传入合并请求时优先按合并请求过滤 @1
- 传入合并请求时不再按代码库过滤 @1
- 状态为 all 时不追加状态条件 @1
- 统计模式只查询总数 @1
- 状态为 converted 时关联 bug 表 @1
- 状态为 converted 时排除已删除的 bug @1

*/

global $tester;

$tester->dao->query('TRUNCATE TABLE ' . TABLE_SCANISSUE);

su('admin');
$test = new codescanModelTest();

r($test->buildManualIssueQueryTest(0, 24, 'unknown') === false) && p() && e('1');

$query = $test->buildManualIssueQueryTest(0, 24, 'wait');
r(is_object($query)) && p() && e('1');

$sql = $query->get();
r(strpos($sql, "t1.scanMethod  = 'manual'") !== false) && p() && e('1');
r(strpos($sql, "t1.repoID  = '24'") !== false)         && p() && e('1');
r(strpos($sql, "t1.status  = 'wait'") !== false)       && p() && e('1');

$sql = $test->buildManualIssueQueryTest(5, 24, 'all')->get();
r(strpos($sql, "t1.ppmID  = '5'") !== false) && p() && e('1');
r(strpos($sql, 't1.repoID') === false)       && p() && e('1');
r(strpos($sql, 't1.status') === false)       && p() && e('1');

$sql = $test->buildManualIssueQueryTest(5, 0, '', true)->get();
r(strpos($sql, 'COUNT(DISTINCT t1.id)') !== false) && p() && e('1');

$sql = $test->buildManualIssueQueryTest(0, 24, 'converted')->get();
r(strpos($sql, 'INNER JOIN ' . TABLE_BUG) !== false) && p() && e('1');
r(strpos($sql, "t2.deleted  = '0'") !== false)       && p() && e('1');
