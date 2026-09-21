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

- 空状态表示全部，不加状态条件  @5
- type=static 且 status=wait 映射为缺陷 active  @2
- type=static 且 status=solved 映射为缺陷 resolved  @1
- type=static 且 status=closed 映射为缺陷 closed  @1
- type=static 不支持的清单状态返回 false  @1
- type=ai 且 status=ignore 映射为缺陷 ignore  @1
- 未注册的 type 对非空状态返回 false  @1
- 返回对象即传入对象，可继续链式调用  @1

*/

su('admin');
$test = new ppmModelTest();
global $tester;

/* 每次现取一个新查询对象，避免条件在上一步的基础上叠加。 */
$buildQuery = function() use ($tester)
{
    return $tester->dao->select('id')->from(TABLE_BUG)->where('repo')->eq(1)->andWhere('deleted')->eq(0);
};

r(count($test->applyIssueStatusConditionTest($buildQuery(), '', 'static')->fetchAll('id'))) && p() && e('5');
r(count($test->applyIssueStatusConditionTest($buildQuery(), 'wait', 'static')->fetchAll('id'))) && p() && e('2');
r(count($test->applyIssueStatusConditionTest($buildQuery(), 'solved', 'static')->fetchAll('id'))) && p() && e('1');
r(count($test->applyIssueStatusConditionTest($buildQuery(), 'closed', 'static')->fetchAll('id'))) && p() && e('1');
r($test->applyIssueStatusConditionTest($buildQuery(), 'ignore', 'static') === false) && p() && e('1');
r(count($test->applyIssueStatusConditionTest($buildQuery(), 'ignore', 'ai')->fetchAll('id'))) && p() && e('1');
r($test->applyIssueStatusConditionTest($buildQuery(), 'wait', 'unknown') === false) && p() && e('1');

$query = $buildQuery();
r($test->applyIssueStatusConditionTest($query, 'wait', 'static') === $query) && p() && e('1');
