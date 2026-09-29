#!/usr/bin/env php
<?php
include dirname(__FILE__, 5) . '/test/lib/init.php';
include dirname(__FILE__, 2) . '/lib/model.class.php';

/**

title=测试 codescanModel->processManualIssueData();
timeout=0
cid=0

- 验证字符串 payload 可解析 @phpstan
- 验证文件与行号范围 @/src/a.php,#10-#12
- 验证问题类型与等级 @bug,high
- 验证未传 repoID 时使用参数兜底 @24
- 验证 HTML 标题会被清洗为纯文本 @人工评审问题
- 验证 content 中的 HTML 实体会被解码 @<p>评审内容</p>
- 验证单行问题起止行号相同 @#128-#128
- 验证起始行缺失时行号范围为空 @

*/

su('admin');
$test = new codescanModelTest();

$issue = new stdclass();
$issue->path        = '/src/a.php';
$issue->startLine   = 10;
$issue->endLine     = 12;
$issue->category    = 'bug';
$issue->severity    = 'high';
$issue->title       = '<p><span>人工评审问题</span></p>';
$issue->content     = '&lt;p&gt;评审内容&lt;/p&gt;';
$issue->payload     = '{"tool":"phpstan"}';
$issue->repoID      = 0;
$result = $test->processManualIssueDataTest($issue, 24);

r($result->rulePlugin) && p() && e('phpstan');
r($result->file . ',' . $result->lineRange) && p() && e('/src/a.php,#10-#12');
r($result->type . ',' . $result->priority) && p() && e('bug,high');
r($result->repoID) && p() && e('24');
r($result->title) && p() && e('人工评审问题');
r($result->content) && p() && e('<p>评审内容</p>');

$emptyLine = new stdclass();
$emptyLine->payload = new stdclass();
$emptyLine = $test->processManualIssueDataTest($emptyLine);
r($emptyLine->lineRange === '') && p() && e('1');

$singleLine = new stdclass();
$singleLine->payload   = new stdclass();
$singleLine->startLine = 128;
$singleLine->endLine   = 128;
r($test->processManualIssueDataTest($singleLine)->lineRange) && p() && e('#128-#128');
