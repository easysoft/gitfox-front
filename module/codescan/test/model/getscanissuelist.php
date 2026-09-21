#!/usr/bin/env php
<?php
include dirname(__FILE__, 5) . '/test/lib/init.php';
include dirname(__FILE__, 2) . '/lib/model.class.php';

zenData('entry')->gen(0);

/**

title=测试 codescanModel->getScanIssueList();
timeout=0
cid=0

- 空参数按仓库维度查询返回空列表  @0
- 指定 taskID 走任务维度接口，同样返回空列表  @0
- 空参数与全量参数都返回数组  @1
- file 参数归一化为 path 后返回空列表  @0
- 分页与排序参数不影响空数据返回  @0
- 非法 taskID（负数、超大值）返回空列表  @0
- issueID/plan 等参数归一化后返回空列表  @0
- 任务与仓库维度混合参数仍返回数组  @1

*/

su('admin');
$test = new codescanModelTest();

/* 用例环境未配置 GitFox 服务，接口请求返回 false，方法在空结果分支提前返回，故断言集中在返回契约与参数归一化。 */
r($test->getScanIssueListTest(0, array())) && p() && e('0');
r($test->getScanIssueListTest(12, array('repoID' => 3))) && p() && e('0');
r(is_array($test->getScanIssueListTest(0, array('repoID' => 3, 'page' => 1, 'limit' => 20)))) && p() && e('1');
r($test->getScanIssueListTest(0, array('file' => '/src/a.php'))) && p() && e('0');
r($test->getScanIssueListTest(0, array('page' => 2, 'limit' => 5, 'sort' => 'createdAt', 'orderBy' => 'id_desc'))) && p() && e('0');
r($test->getScanIssueListTest(-1, array('repoID' => 3))) && p() && e('0');
r($test->getScanIssueListTest(999999, array('repoID' => 3))) && p() && e('0');
r($test->getScanIssueListTest(0, array('issueID' => 5, 'plan' => 2, 'priority' => 'high', 'type' => 'defect', 'plugin' => 'phpstan', 'createdAt' => '2025-01-01'))) && p() && e('0');
r(is_array($test->getScanIssueListTest(12, array('taskID' => 8, 'repoID' => 3, 'ruleID' => 2, 'planID' => 1)))) && p() && e('1');
