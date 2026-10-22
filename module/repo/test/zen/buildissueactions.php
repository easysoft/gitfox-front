#!/usr/bin/env php
<?php
include dirname(__FILE__, 5) . '/test/lib/init.php';
include dirname(__FILE__, 2) . '/lib/zen.class.php';

global $tester;

zenData('user')->gen(2);
zenData('group')->gen(0);
zenData('usergroup')->gen(0);
zenData('grouppriv')->gen(0);

$group = zenData('group');
$group->id->range('1');
$group->name->range('repo-issue-actions');
$group->project->range('0');
$group->devopsSpace->range('0');
$group->vision->range($tester->config->vision);
$group->gen(1);

$userGroup = zenData('usergroup');
$userGroup->account->range('user1');
$userGroup->group->range('1');
$userGroup->gen(1);

$groupPriv = zenData('grouppriv');
$groupPriv->group->range('1,1');
$groupPriv->module->range('codescan{2}');
$groupPriv->method->range('resolveManualIssue,closeManualIssue');
$groupPriv->gen(2);

$buildIssue = function($id, $scanMethod = '', $actions = null, $status = 'wait')
{
    $issue = new stdclass();
    $issue->id = $id;
    $issue->status = $status;
    if($scanMethod !== '') $issue->scanMethod = $scanMethod;
    if($actions !== null) $issue->actions = $actions;
    return $issue;
};

$test = new repoZenTest();
su('admin');

/**

title=测试 repoZen::buildIssueActions();
timeout=0
cid=0

- 步骤1：空问题列表不生成动作 @0
- 步骤2：管理员为人工审查问题生成解决和关闭动作 @resolve,close
- 步骤3：人工审查问题的解决动作使用人工审查方法并携带问题ID @1
- 步骤4：人工审查问题的关闭动作使用人工审查方法并携带问题ID @1
- 步骤5：管理员为代码检查问题生成解决和关闭动作 @resolve,close
- 步骤6：代码检查问题的解决动作使用通用解决方法并携带问题ID @1
- 步骤7：代码检查问题的关闭动作使用通用关闭方法并携带问题ID @1
- 步骤8：管理员为代码异味静态扫描问题生成解决和关闭动作 @resolve,close
- 步骤9：代码异味问题的解决和关闭动作使用通用方法并携带问题ID @1
- 步骤10：管理员为AI问题生成解决和关闭动作 @resolve,close
- 步骤11：AI问题的解决和关闭动作使用通用方法并携带问题ID @1
- 步骤12：已有动作会被重新构建 @resolve,close
- 步骤13：重新构建后不保留旧动作 @0
- 步骤14：未知扫描类型保留已有动作 @/existing
- 步骤15：缺少扫描类型不会生成动作 @0
- 步骤16：混合类型问题分别生成正确的动作数量 @2|2|2|2|0|0
- 步骤17：仅有角色权限时人工审查问题生成两个动作 @resolve,close
- 步骤18：无代码检查权限时检查问题不生成动作 @0
- 步骤19：无静态扫描权限时代码异味问题不生成动作 @0
- 步骤20：无AI权限时AI问题不生成动作 @0
- 步骤21：无代码检查权限时清空已有动作 @0
- 步骤22：问题操作动作URL携带刷新回调 @1
- 步骤23：已解决静态扫描问题只展示关闭动作 @close
- 步骤24：已忽略AI问题只展示关闭动作 @close
- 步骤25：已忽略人工审查问题不展示动作 @0
- 步骤26：已关闭问题不展示动作 @0
- 步骤27：受限用户按角色权限查看自己创建问题的操作 @2
- 步骤28：受限用户按角色权限查看他人创建问题的操作 @2
- 步骤29：核心构建的问题动作只包含解决和关闭 @resolve,close
- 步骤30：核心构建的问题动作不包含转Bug @0
- 步骤31：非激活状态不展示转Bug动作 @close

*/

$actions = $test->buildIssueActionsTest(array());
r(count($actions)) && p() && e('0'); // 步骤1：空问题列表不生成动作

$actions = $test->buildIssueActionsTest(array($buildIssue(11, 'manual')));
r(implode(',', array_keys($actions[11]))) && p() && e('resolve,close'); // 步骤2：管理员为人工审查问题生成解决和关闭动作
r(stripos($actions[11]['resolve'], 'f=resolvemanualissue&issueID=11') !== false) && p() && e('1'); // 步骤3：人工审查问题的解决动作使用人工审查方法并携带问题ID
r(stripos($actions[11]['close'], 'f=closemanualissue&issueID=11') !== false) && p() && e('1'); // 步骤4：人工审查问题的关闭动作使用人工审查方法并携带问题ID

$actions = $test->buildIssueActionsTest(array($buildIssue(12, 'check')));
r(implode(',', array_keys($actions[12]))) && p() && e('resolve,close'); // 步骤5：管理员为代码检查问题生成解决和关闭动作
r(stripos($actions[12]['resolve'], 'f=resolveissue&issueID=12') !== false) && p() && e('1'); // 步骤6：代码检查问题的解决动作使用通用解决方法并携带问题ID
r(stripos($actions[12]['close'], 'f=closeissue&issueID=12') !== false) && p() && e('1'); // 步骤7：代码检查问题的关闭动作使用通用关闭方法并携带问题ID

$actions = $test->buildIssueActionsTest(array($buildIssue(17, 'smell')));
r(implode(',', array_keys($actions[17]))) && p() && e('resolve,close'); // 步骤8：管理员为代码异味静态扫描问题生成解决和关闭动作
r(stripos($actions[17]['resolve'], 'f=resolveissue&issueID=17') !== false && stripos($actions[17]['close'], 'f=closeissue&issueID=17') !== false && stripos($actions[17]['resolve'], 'smellissue') === false) && p() && e('1'); // 步骤9：代码异味问题的解决和关闭动作使用通用方法并携带问题ID

$actions = $test->buildIssueActionsTest(array($buildIssue(13, 'ai')));
r(implode(',', array_keys($actions[13]))) && p() && e('resolve,close'); // 步骤10：管理员为AI问题生成解决和关闭动作
r(stripos($actions[13]['resolve'], 'f=resolveissue&issueID=13') !== false && stripos($actions[13]['close'], 'f=closeissue&issueID=13') !== false && stripos($actions[13]['resolve'], 'aiissue') === false) && p() && e('1'); // 步骤11：AI问题的解决和关闭动作使用通用方法并携带问题ID

$actions = $test->buildIssueActionsTest(array($buildIssue(14, 'manual', array('existing' => '/existing'))));
r(implode(',', array_keys($actions[14]))) && p() && e('resolve,close'); // 步骤12：已有动作会被重新构建
r(isset($actions[14]['existing'])) && p() && e('0'); // 步骤13：重新构建后不保留旧动作

$actions = $test->buildIssueActionsTest(array($buildIssue(15, 'unknown', array('existing' => '/existing'))));
r($actions[15]['existing']) && p() && e('/existing'); // 步骤14：未知扫描类型保留已有动作

$actions = $test->buildIssueActionsTest(array($buildIssue(16)));
r(count($actions[16])) && p() && e('0'); // 步骤15：缺少扫描类型不会生成动作

$actions = $test->buildIssueActionsTest(array($buildIssue(21, 'manual'), $buildIssue(22, 'check'), $buildIssue(23, 'smell'), $buildIssue(24, 'ai'), $buildIssue(25, 'unknown'), $buildIssue(26)));
r(implode('|', array_map('count', $actions))) && p() && e('2|2|2|2|0|0'); // 步骤16：混合类型问题分别生成正确的动作数量

su('user1');

$actions = $test->buildIssueActionsTest(array($buildIssue(31, 'manual')));
r(implode(',', array_keys($actions[31]))) && p() && e('resolve,close'); // 步骤17：仅有角色权限时人工审查问题生成两个动作

$actions = $test->buildIssueActionsTest(array($buildIssue(32, 'check')));
r(count($actions[32])) && p() && e('0'); // 步骤18：无代码检查权限时检查问题不生成动作

$actions = $test->buildIssueActionsTest(array($buildIssue(34, 'smell')));
r(count($actions[34])) && p() && e('0'); // 步骤19：无静态扫描权限时代码异味问题不生成动作

$actions = $test->buildIssueActionsTest(array($buildIssue(33, 'ai')));
r(count($actions[33])) && p() && e('0'); // 步骤20：无AI权限时AI问题不生成动作

$actions = $test->buildIssueActionsTest(array($buildIssue(35, 'check', array('existing' => '/existing'))));
r(count($actions[35])) && p() && e('0'); // 步骤21：无代码检查权限时清空已有动作

su('admin');

$actions = $test->buildIssueActionsTest(array($buildIssue(41, 'manual'), $buildIssue(42, 'check'), $buildIssue(43, 'smell'), $buildIssue(44, 'ai')));
$callbackOK = true;
foreach(array(41, 42, 43, 44) as $issueID)
{
    $callbackOK = $callbackOK && strpos($actions[$issueID]['resolve'], 'callback=refreshScanIssue') !== false && strpos($actions[$issueID]['close'], 'from=&callback=refreshScanIssue') !== false;
}
r($callbackOK) && p() && e('1'); // 步骤22：问题操作动作URL携带刷新回调

$actions = $test->buildIssueActionsTest(array($buildIssue(51, 'check', null, 'solved')));
r(implode(',', array_keys($actions[51]))) && p() && e('close'); // 步骤23：已解决静态扫描问题只展示关闭动作

$actions = $test->buildIssueActionsTest(array($buildIssue(52, 'ai', null, 'ignore')));
r(implode(',', array_keys($actions[52]))) && p() && e('close'); // 步骤24：已忽略AI问题只展示关闭动作

$actions = $test->buildIssueActionsTest(array($buildIssue(53, 'manual', null, 'ignore')));
r(count($actions[53])) && p() && e('0'); // 步骤25：已忽略人工审查问题不展示动作

$actions = $test->buildIssueActionsTest(array($buildIssue(54, 'check', null, 'closed')));
r(count($actions[54])) && p() && e('0'); // 步骤26：已关闭问题不展示动作

su('user1');
$tester->app->user->rights['rights']['my']['limited'] = true;

$ownIssue = $buildIssue(61, 'manual');
$ownIssue->createdBy = 'user1';
$otherIssue = $buildIssue(62, 'manual');
$otherIssue->createdBy = 'user2';
$actions = $test->buildIssueActionsTest(array($ownIssue, $otherIssue));
r(count($actions[61])) && p() && e('2'); // 步骤27：受限用户按角色权限查看自己创建问题的操作
r(count($actions[62])) && p() && e('2'); // 步骤28：受限用户按角色权限查看他人创建问题的操作

su('admin');

$bugIssue = $buildIssue(71, 'manual');
$bugIssue->repoID = 1;
$bugIssue->productID = 1;
$actions = $test->buildIssueActionsTest(array($bugIssue));
r(implode(',', array_keys($actions[71]))) && p() && e('resolve,close'); // 步骤29：核心构建的问题动作只包含解决和关闭
r(isset($actions[71]['bug'])) && p() && e('0'); // 步骤30：核心构建的问题动作不包含转Bug

$solvedIssue = $buildIssue(72, 'manual', null, 'solved');
$solvedIssue->repoID = 1;
$solvedIssue->productID = 1;
$actions = $test->buildIssueActionsTest(array($solvedIssue));
r(implode(',', array_keys($actions[72]))) && p() && e('close'); // 步骤31：非激活状态不展示转Bug动作
