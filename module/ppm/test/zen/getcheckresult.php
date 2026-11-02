#!/usr/bin/env php
<?php

/**

title=测试 ppmZen::getCheckResult();
timeout=0
cid=0

- 执行$result @1
- 执行conflictFiles) && is_array($result模块的conflictFiles方法  @1
- 执行$result->message, '有未解决的缺陷类型的问题，请先解决。') !== false @1
- 执行$result->message, '缺陷') !== false @1
- 执行$result->message, '有未解决的类型的问题') === false @1
- 执行$result->message, '性能问题') !== false @1
- 执行$result->message, '有未解决的类型的问题') === false @1

*/

include dirname(__FILE__, 5) . '/test/lib/init.php';
include dirname(__FILE__, 2) . '/lib/zen.class.php';

global $app;
$app->rawModule = 'ppm';
$app->rawMethod = 'view';
$app->setMethodName('view');

zenData('entry')->loadYaml('entry', false, 2)->gen(1);
zenData('user')->gen(3);

$ppmTable = zenData('ops_ppm')->loadYaml('ops_ppm', false, 2);
$ppmTable->id->range('9101');
$ppmTable->repoID->range('42');
$ppmTable->sourceRepoID->range('42');
$ppmTable->sourceBranch->range('feature/demo');
$ppmTable->targetRepoID->range('42');
$ppmTable->targetBranch->range('release/main');
$ppmTable->gen(1);

$branchRule = zenData('ops_branch_ruleset');
$branchRule->id->range('9201');
$branchRule->repo->range('42');
$branchRule->branchType->range('0');
$branchRule->branchName->range('release/main');
$branchRule->ppmHandleUser->range('admin');
$branchRule->reviewFlowID->range('9301');
$branchRule->deleted->range('0');
$branchRule->gen(1);

$reviewFlow = zenData('ops_review_flow');
$reviewFlow->id->range('9301');
$reviewFlow->repo->range('42');
$reviewFlow->name->range('问题类型检查流程');
$reviewFlow->definition->range('test');
$reviewFlow->status->range('enable');
$reviewFlow->deleted->range('0');
$reviewFlow->gen(1);

su('admin');

$lang->ppm->aiCodeReview = new stdclass();
$lang->ppm->aiCodeReview->issueTypeList = array('performance' => '性能问题');

$ppmZen = new ppmZenTest();
$ppm    = (object)array('id' => 9101, 'repoID' => 42, 'targetRepoID' => 42, 'sourceBranch' => 'feature/demo', 'targetBranch' => 'release/main', 'reviewStatus' => 'pending');
$issues = array((object)array('status' => 'wait', 'type' => 'defect'));
$flowDefinition = '{"reviewFlow":{"issues":{"addressOption":"specificMustBeSolved","mandatoryType":["defect"]}}}';
$ppmZen->instance->dao->update(TABLE_REVIEWFLOW)->set('definition')->eq($flowDefinition)->where('id')->eq(9301)->exec();

$result = $ppmZen->getCheckResultTest($ppm, 'approved', $issues);
r(is_object($result)) && p() && e('1');
r(isset($result->conflictFiles) && is_array($result->conflictFiles)) && p() && e('1');
r(strpos($result->message, '有未解决的缺陷类型的问题，请先解决。') !== false) && p() && e('1');
r(strpos($result->message, '缺陷') !== false) && p() && e('1');
r(strpos($result->message, '有未解决的类型的问题') === false) && p() && e('1');

$issues = array((object)array('status' => 'wait', 'category' => 'performance'));
$flowDefinition = '{"reviewFlow":{"issues":{"addressOption":"specificMustBeSolved","mandatoryType":["performance"]}}}';
$ppmZen->instance->dao->update(TABLE_REVIEWFLOW)->set('definition')->eq($flowDefinition)->where('id')->eq(9301)->exec();

$result = $ppmZen->getCheckResultTest($ppm, 'approved', $issues);
r(strpos($result->message, '性能问题') !== false) && p() && e('1');
r(strpos($result->message, '有未解决的类型的问题') === false) && p() && e('1');