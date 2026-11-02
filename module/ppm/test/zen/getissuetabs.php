#!/usr/bin/env php
<?php
include dirname(__FILE__, 5) . '/test/lib/init.php';
include dirname(__FILE__, 2) . '/lib/zen.class.php';

/**

title=测试 ppmZen->getIssueTabs();
timeout=0
cid=0

- 执行$tabs) ? implode(',', array_keys(array_intersect_key($tabs, array('static' => 1, 'manual' => 1)))) :  @static,manual

- 执行$tabs) && isset($tabs['static']) ? $tabs['static'] :  @静态扫描问题
- 执行$tabs) && isset($tabs['manual']) ? $tabs['manual'] :  @人工审查问题
- 执行$tabs) && isset($tabs['scan']) ? 1 : 0 @0

*/

global $app, $lang;
$app->rawModule = 'ppm';
$app->rawMethod = 'view';
$app->setMethodName('view');
$app->loadLang('ppm');

zenData('entry')->loadYaml('entry', false, 2)->gen(1);
zenData('user')->gen(3);
su('admin');

$test = new ppmZenTest();
$tabs = $test->getIssueTabsTest();

r(is_array($tabs) ? implode(',', array_keys(array_intersect_key($tabs, array('static' => 1, 'manual' => 1)))) : '') && p() && e('static,manual');
r(is_array($tabs) && isset($tabs['static']) ? $tabs['static'] : '') && p() && e('静态扫描问题');
r(is_array($tabs) && isset($tabs['manual']) ? $tabs['manual'] : '') && p() && e('人工审查问题');
r(is_array($tabs) && isset($tabs['scan']) ? 1 : 0) && p() && e('0');
