#!/usr/bin/env php
<?php
include dirname(__FILE__, 5) . '/test/lib/init.php';
include dirname(__FILE__, 2) . '/lib/zen.class.php';

/**

title=测试 codescanZen->getIssueDistribution();
timeout=0
cid=0

- 空数组入参返回空分布  @0
- 空对象入参返回空分布  @0
- status 分布过滤历史状态 todo/solving，只保留 4 项  @4
- status 分布按 issueStatusList 映射计数，缺失状态补 0  @3,0,0,1
- status 分布传对象形态的计数同样生效  @4
- priority 分布跳过 all，保留 3 个等级  @3
- priority 分布按 severityList 映射计数  @5,2,0
- 未定义类型按 typeList 分布  @4
- 未定义类型按 typeList 映射计数  @7,1,0,0
- 多类型入参分别产出各自的分布  @2

*/

su('admin');
$test = new codescanZenTest();

r(count($test->getIssueDistributionTest(array()))) && p() && e('0');
r(count($test->getIssueDistributionTest(new stdclass()))) && p() && e('0');

$statusMetrics = array('status' => array('wait' => 3, 'todo' => 9, 'solving' => 8, 'closed' => 1));
$statusResult  = $test->getIssueDistributionTest($statusMetrics);
r(count($statusResult['status'])) && p() && e('4');
r(implode(',', array_column($statusResult['status'], 'value'))) && p() && e('3,0,0,1');
r(count($test->getIssueDistributionTest((object)array('status' => (object)array('wait' => 2)))['status'])) && p() && e('4');

$priorityMetrics = array('priority' => array('high' => 5, 'medium' => 2));
$priorityResult  = $test->getIssueDistributionTest($priorityMetrics);
r(count($priorityResult['priority'])) && p() && e('3');
r(implode(',', array_column($priorityResult['priority'], 'value'))) && p() && e('5,2,0');

$typeMetrics = array('category' => array('defect' => 7, 'security' => 1));
$typeResult  = $test->getIssueDistributionTest($typeMetrics);
r(count($typeResult['category'])) && p() && e('4');
r(implode(',', array_column($typeResult['category'], 'value'))) && p() && e('7,1,0,0');

r(count($test->getIssueDistributionTest(array('status' => array('wait' => 1), 'priority' => array('high' => 1))))) && p() && e('2');
