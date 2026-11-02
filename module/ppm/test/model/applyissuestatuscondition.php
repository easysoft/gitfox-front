#!/usr/bin/env php
<?php
include dirname(__FILE__, 5) . '/test/lib/init.php';
include dirname(__FILE__, 2) . '/lib/model.class.php';

$bug = zenData('bug');
$bug->id->range('1-7');
$bug->repo->range('1,1,1,1,1,1,2');
$bug->status->range('active,active,resolved,closed,ignore,active,active');
$bug->deleted->range('0,0,0,0,0,1,0');
$bug->gen(7);

/**

title=测试 ppmModel->applyIssueStatusCondition();
timeout=0
cid=0

- 执行applyIssueStatusConditionTest($buildQuery(), '', 'bug')模块的fetchAll方法，参数是'id'  @5
- 执行applyIssueStatusConditionTest($buildQuery(), 'wait', 'bug')模块的fetchAll方法，参数是'id'  @2
- 执行applyIssueStatusConditionTest($buildQuery(), 'solved', 'bug')模块的fetchAll方法，参数是'id'  @1
- 执行applyIssueStatusConditionTest($buildQuery(), 'closed', 'bug')模块的fetchAll方法，参数是'id'  @1
- 执行test模块的applyIssueStatusConditionTest方法，参数是$buildQuery  @1
- 执行test模块的applyIssueStatusConditionTest方法，参数是$buildQuery  @1
- 执行test模块的applyIssueStatusConditionTest方法，参数是$buildQuery  @1
- 执行test模块的applyIssueStatusConditionTest方法，参数是$query, 'wait', 'bug') === $query  @1

*/

su('admin');
$test = new ppmModelTest();
global $tester;

/* 每次现取一个新查询对象，避免条件在上一步的基础上叠加。 */
$buildQuery = function() use ($tester)
{
    return $tester->dao->select('id')->from(TABLE_BUG)->where('repo')->eq(1)->andWhere('deleted')->eq(0);
};

r(count($test->applyIssueStatusConditionTest($buildQuery(), '', 'bug')->fetchAll('id'))) && p() && e('5');
r(count($test->applyIssueStatusConditionTest($buildQuery(), 'wait', 'bug')->fetchAll('id'))) && p() && e('2');
r(count($test->applyIssueStatusConditionTest($buildQuery(), 'solved', 'bug')->fetchAll('id'))) && p() && e('1');
r(count($test->applyIssueStatusConditionTest($buildQuery(), 'closed', 'bug')->fetchAll('id'))) && p() && e('1');
r($test->applyIssueStatusConditionTest($buildQuery(), 'ignore', 'bug') === false) && p() && e('1');
r($test->applyIssueStatusConditionTest($buildQuery(), 'ignore', 'ai') === false) && p() && e('1');
r($test->applyIssueStatusConditionTest($buildQuery(), 'wait', 'unknown') === false) && p() && e('1');

$query = $buildQuery();
r($test->applyIssueStatusConditionTest($query, 'wait', 'bug') === $query) && p() && e('1');
