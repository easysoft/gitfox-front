#!/usr/bin/env php
<?php
include dirname(__FILE__, 5) . '/test/lib/init.php';
include dirname(__FILE__, 2) . '/lib/model.class.php';
zenData('entry')->loadYaml('entry', false, 2)->gen(1, true, false);

/**

title=测试 codescanModel->resendTask();
timeout=0
cid=0

- 执行test模块的resendTaskTest方法，参数是1  @0
- 执行test模块的resendTaskTest方法，参数是2  @0
- 执行test模块的resendTaskTest方法，参数是3  @0
- 执行test模块的resendTaskTest方法，参数是4  @0
- 执行test模块的resendTaskTest方法  @0

*/

su('admin');
$test = new codescanModelTest();

r($test->resendTaskTest(1)) && p() && e('0');
r($test->resendTaskTest(2)) && p() && e('0');
r($test->resendTaskTest(3)) && p() && e('0');
r($test->resendTaskTest(4)) && p() && e('0');
r($test->resendTaskTest(0)) && p() && e('0');