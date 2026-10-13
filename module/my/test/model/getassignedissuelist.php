#!/usr/bin/env php
<?php

/**

title=测试 myModel::getAssignedIssueList();
timeout=0
cid=0

- 步骤1：默认返回我创建的代码评审上的人工审查问题数量 @2
- 步骤2：默认列表第一条为当前用户评审上的问题 @My issue 1
- 步骤3：按状态wait过滤返回激活的问题 @1
- 步骤4：状态为all时返回全部状态的问题 @2
- 步骤5：不包含他人创建的代码评审上的问题 @0
- 步骤6：不包含非人工审查问题 @0
- 步骤7：不包含已删除代码评审上的问题 @0

*/

include dirname(__FILE__, 5) . '/test/lib/init.php';
include dirname(__FILE__, 2) . '/lib/model.class.php';

zenData('user')->gen(5);

/* 代码评审数据：1号由当前用户创建，2号由 user1 创建，3号已删除。 */
$ppm = zenData('ops_ppm');
$ppm->id->range('1-3');
$ppm->title->range('Review 1,Review 2,Review 3');
$ppm->createdBy->range('admin,user1,admin');
$ppm->deleted->range('0,0,1');
$ppm->gen(3);

/* 人工审查问题数据：4号属于他人评审，5号非人工审查问题，6号已删除，7号属于已删除的评审。 */
$tester->dao->delete()->from(TABLE_SCANISSUE)->exec();
$issues = array(
    array('id' => 1, 'issueKey' => 'ut-issue-1', 'ppmID' => 1, 'repoID' => 1, 'scanMethod' => 'manual', 'status' => 'wait',   'title' => 'My issue 1',     'deleted' => 0),
    array('id' => 2, 'issueKey' => 'ut-issue-2', 'ppmID' => 1, 'repoID' => 1, 'scanMethod' => 'manual', 'status' => 'closed', 'title' => 'My issue 2',     'deleted' => 0),
    array('id' => 3, 'issueKey' => 'ut-issue-3', 'ppmID' => 2, 'repoID' => 1, 'scanMethod' => 'manual', 'status' => 'wait',   'title' => 'Other issue',    'deleted' => 0),
    array('id' => 4, 'issueKey' => 'ut-issue-4', 'ppmID' => 1, 'repoID' => 1, 'scanMethod' => 'check',  'status' => 'wait',   'title' => 'Scan issue',     'deleted' => 0),
    array('id' => 5, 'issueKey' => 'ut-issue-5', 'ppmID' => 3, 'repoID' => 1, 'scanMethod' => 'manual', 'status' => 'wait',   'title' => 'Deleted ppm',    'deleted' => 0),
    array('id' => 6, 'issueKey' => 'ut-issue-6', 'ppmID' => 1, 'repoID' => 1, 'scanMethod' => 'manual', 'status' => 'wait',   'title' => 'Deleted issue',  'deleted' => 1)
);
foreach($issues as $issue) $tester->dao->insert(TABLE_SCANISSUE)->data((object)$issue)->exec();

su('admin');

global $tester;
$tester->app->loadClass('pager', true);
$tester->app->rawModule = 'my';
$tester->app->rawMethod = 'assignedissue';

$myModel = new myModelTest();

r(count($myModel->getAssignedIssueListTest()))            && p() && e('2');           // 步骤1：默认返回我创建的代码评审上的人工审查问题数量
r($myModel->getAssignedIssueListTest())                   && p('1:title') && e('My issue 1'); // 步骤2：默认列表第一条为当前用户评审上的问题
r(count($myModel->getAssignedIssueListTest(null, 'wait'))) && p() && e('1');          // 步骤3：按状态wait过滤返回激活的问题
r(count($myModel->getAssignedIssueListTest(null, 'all')))  && p() && e('2');          // 步骤4：状态为all时返回全部状态的问题
r(isset($myModel->getAssignedIssueListTest()[3]) ? 1 : 0)  && p() && e('0');          // 步骤5：不包含他人创建的代码评审上的问题
r(isset($myModel->getAssignedIssueListTest()[4]) ? 1 : 0)  && p() && e('0');          // 步骤6：不包含非人工审查问题
r(isset($myModel->getAssignedIssueListTest()[5]) ? 1 : 0)  && p() && e('0');          // 步骤7：不包含已删除代码评审上的问题
