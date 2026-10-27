#!/usr/bin/env php
<?php
include dirname(__FILE__, 5) . '/test/lib/init.php';
include dirname(__FILE__, 2) . '/lib/model.class.php';

/**

title=测试 codescanModel->processIssueData();
timeout=0
cid=0

- 执行$result->file . ',' . $result->priority . ',' . $result->type . ',' . $result->rulePlugin @/src/a.php,high,bug,phpstan

- 执行$result->title ===  @1
- 执行startLine) && is_int($result模块的endLine) ? 1 : 0方法  @1
- 执行$result->ppmID) ? 1 : 0 @1
- 执行$result->scanMethod === '' && $result->category === '' && $result->severity === '' ? 1 : 0 @1
- 执行processIssueDataTest($existing)模块的title方法  @已存在问题
- 执行processIssueDataTest($missingPlugin)模块的rulePlugin === '' ? 1 : 0方法  @1
- 执行processIssueDataTest($stringPayload)模块的rulePlugin方法  @golangci-lint
- 执行processIssueDataTest($topLevelPlugin)模块的rulePlugin方法  @clippy

*/

su('admin');
$test = new codescanModelTest();

$issue = new stdclass();
$issue->path         = '/src/a.php';
$issue->rulePriority = 'high';
$issue->ruleType     = 'bug';
$issue->payload      = (object)array('tool' => 'phpstan');
$result = $test->processIssueDataTest($issue);

r($result->file . ',' . $result->priority . ',' . $result->type . ',' . $result->rulePlugin) && p() && e('/src/a.php,high,bug,phpstan');
r($result->title === '') && p() && e('1');
r(is_int($result->startLine) && is_int($result->endLine) ? 1 : 0) && p() && e('1');
r(is_int($result->ppmID) ? 1 : 0) && p() && e('1');
r($result->scanMethod === '' && $result->category === '' && $result->severity === '' ? 1 : 0) && p() && e('1');

$existing = new stdclass();
$existing->title   = '已存在问题';
$existing->payload = new stdclass();
r($test->processIssueDataTest($existing)->title) && p() && e('已存在问题');

$missingPlugin = new stdclass();
$missingPlugin->payload = null;
r($test->processIssueDataTest($missingPlugin)->rulePlugin === '' ? 1 : 0) && p() && e('1');

$stringPayload = new stdclass();
$stringPayload->payload = '{"tool":"golangci-lint"}';
r($test->processIssueDataTest($stringPayload)->rulePlugin) && p() && e('golangci-lint');

$topLevelPlugin = new stdclass();
$topLevelPlugin->rulePlugin = 'clippy';
$topLevelPlugin->payload    = null;
r($test->processIssueDataTest($topLevelPlugin)->rulePlugin) && p() && e('clippy');