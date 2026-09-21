#!/usr/bin/env php
<?php
include dirname(__FILE__, 5) . '/test/lib/init.php';
include dirname(__FILE__, 2) . '/lib/model.class.php';

zenData('user')->gen(5);

$ppm = zenData('ops_ppm');
$ppm->id->range('1-4');
$ppm->createdBy->range('admin,admin,admin,user1');
$ppm->status->range('opened,opened,closed,opened');
$ppm->deleted->range('0');
$ppm->gen(4);

$issue = zenData('ops_scan_issues');
$issue->id->range('1-6');
$issue->issueKey->prefix('key-')->range('1-6');
$issue->ppmID->range('1,1,1,2,4,4');
$issue->status->range('wait,todo,solving,wait,wait,wait');
$issue->deleted->range('0,0,0,0,1,0');
$issue->gen(6);

/**

title=测试 myModel->getMyReviewStatistics();
timeout=0
cid=0

- admin 发起的评审数，已关闭的评审不计  @2
- admin 名下的待解决问题数，历史状态 todo/solving 不计  @2
- user1 发起的评审数  @1
- user1 名下的待解决问题数，他人评审与已删除问题不计  @1
- 返回结构为 createdReviews 与 pendingIssues 两个键  @createdReviews,pendingIssues
- 全部评审关闭后发起评审数为 0  @0
- 待解决问题数不受评审关闭影响，仍为 2  @2
- 无任何数据的账号统计全为 0  @0

*/

su('admin');
$test = new myModelTest();

r($test->getMyReviewStatisticsTest()['createdReviews']) && p() && e('2');
r($test->getMyReviewStatisticsTest()['pendingIssues']) && p() && e('2');

su('user1');
r($test->getMyReviewStatisticsTest()['createdReviews']) && p() && e('1');
r($test->getMyReviewStatisticsTest()['pendingIssues']) && p() && e('1');

su('admin');
r(implode(',', array_keys($test->getMyReviewStatisticsTest()))) && p() && e('createdReviews,pendingIssues');

global $tester;
$tester->dao->update(TABLE_PPM)->set('status')->eq('closed')->exec();
r($test->getMyReviewStatisticsTest()['createdReviews']) && p() && e('0');
r($test->getMyReviewStatisticsTest()['pendingIssues']) && p() && e('2');

su('user2');
r($test->getMyReviewStatisticsTest()['createdReviews'] + $test->getMyReviewStatisticsTest()['pendingIssues']) && p() && e('0');
