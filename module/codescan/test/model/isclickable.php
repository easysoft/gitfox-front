#!/usr/bin/env php
<?php
include dirname(__FILE__, 5) . '/test/lib/init.php';
include dirname(__FILE__, 2) . '/lib/model.class.php';

/**

title=测试 codescanModel->isClickable();
timeout=0
cid=0

- 执行test模块的isClickableTest方法，参数是$cs1, 'enable'  @1
- 执行test模块的isClickableTest方法，参数是$cs2, 'disable'  @1
- 执行test模块的isClickableTest方法，参数是$cs3, 'enable'  @0
- 执行test模块的isClickableTest方法，参数是$cs4, 'disable'  @0
- 执行test模块的isClickableTest方法，参数是$cs7, 'resolveissue'  @1
- 执行test模块的isClickableTest方法，参数是$cs8, 'resolveissue'  @0
- 执行test模块的isClickableTest方法，参数是$cs11, 'closeissue'  @1
- 执行test模块的isClickableTest方法，参数是$cs12, 'closeissue'  @0

*/

su('admin');
$test = new codescanModelTest();

$cs1 = new stdclass(); $cs1->status = 'disabled';
r($test->isClickableTest($cs1, 'enable')) && p() && e('1');

$cs2 = new stdclass(); $cs2->status = 'enabled';
r($test->isClickableTest($cs2, 'disable')) && p() && e('1');

$cs3 = new stdclass(); $cs3->status = 'enabled';
r($test->isClickableTest($cs3, 'enable')) && p() && e('0');

$cs4 = new stdclass(); $cs4->status = 'disabled';
r($test->isClickableTest($cs4, 'disable')) && p() && e('0');

$cs7 = new stdclass(); $cs7->status = 'wait';
r($test->isClickableTest($cs7, 'resolveissue')) && p() && e('1');

$cs8 = new stdclass(); $cs8->status = 'solved';
r($test->isClickableTest($cs8, 'resolveissue')) && p() && e('0');

$cs11 = new stdclass(); $cs11->status = 'wait';
r($test->isClickableTest($cs11, 'closeissue')) && p() && e('1');

$cs12 = new stdclass(); $cs12->status = 'closed';
r($test->isClickableTest($cs12, 'closeissue')) && p() && e('0');
