#!/usr/bin/env php
<?php
declare(strict_types=1);

/**

title=测试 upgradeZen::getUpgradeVersions();
timeout=0
cid=0

- 测试开源版来源版本 18_1 的升级版本列表 @ipd5_7,22_7
- 测试专业版来源版本 pro8_7 的升级版本列表 @ipd5_7,22_7
- 测试GitFox来源版本 gitfox4_3 的升级版本列表 @ipd5_7,22_7
- 测试IPD版来源版本 ipd5_6 的升级版本列表 @22_7
- 测试来源版本等于已安装版本时的升级版本列表 @ipd5_7,22_7
- 测试当前版本低于已安装版本时的升级版本列表 @0
- 测试当前版本低于已安装版本时记录降级错误 @不支持降级升级：升级目标版本不能低于当前独立版的基线版本。
- 测试GitFox独立版从4.3升级到4.4时的目标版本 @gitfox4_4
- 测试GitFox独立版已是4.4时的升级版本列表 @0
- 测试GitFox独立版从不低于4.4映射版本的开源版升级时的目标列表 @gitfox4_3,gitfox4_4
- 测试GitFox独立版当前基线低于已安装版本时记录降级错误 @不支持降级升级：升级目标版本不能低于当前独立版的基线版本。
- 测试GitFox独立版从4.3升级到4.4时不记录错误 @0

*/

include dirname(__FILE__, 5) . '/test/lib/init.php';
include dirname(__FILE__, 2) . '/lib/zen.class.php';

zenData('user')->loadYaml('user')->gen(5);

su('admin');

$upgradeZen = new upgradeZenTest();

r($upgradeZen->getUpgradeVersionsTest('18_1', '22_7', '22_7', 'ipd'))                             && p() && e('ipd5_7,22_7');   // 测试开源版来源版本 18_1 的升级版本列表
r($upgradeZen->getUpgradeVersionsTest('pro8_7', '22_7', '22_7', 'ipd'))                           && p() && e('ipd5_7,22_7');   // 测试专业版来源版本 pro8_7 的升级版本列表
r($upgradeZen->getUpgradeVersionsTest('gitfox4_3', '22_7', '22_7', 'ipd'))                        && p() && e('ipd5_7,22_7');   // 测试GitFox来源版本 gitfox4_3 的升级版本列表
r($upgradeZen->getUpgradeVersionsTest('ipd5_6', '22_7', '22_7', 'ipd'))                           && p() && e('22_7');          // 测试IPD版来源版本 ipd5_6 的升级版本列表
r($upgradeZen->getUpgradeVersionsTest('22_7', '22_7', '22_7', 'ipd'))                             && p() && e('ipd5_7,22_7');   // 测试来源版本等于已安装版本时的升级版本列表
r($upgradeZen->getUpgradeVersionsTest('18_1', '22_7', '18_1', 'ipd'))                             && p() && e('0');             // 测试当前版本低于已安装版本时的升级版本列表
r($upgradeZen->getUpgradeVersionsErrorTest('18_1', '22_7', '18_1', 'ipd'))                        && p() && e('不支持降级升级：升级目标版本不能低于当前独立版的基线版本。'); // 测试当前版本低于已安装版本时记录降级错误
r($upgradeZen->getUpgradeVersionsTest('gitfox4_3', 'gitfox4_3', 'gitfox4_4', 'gitfox'))           && p() && e('gitfox4_4');     // 测试GitFox独立版从4.3升级到4.4时的目标版本
r($upgradeZen->getUpgradeVersionsTest('gitfox4_4', 'gitfox4_4', 'gitfox4_4', 'gitfox'))           && p() && e('0');             // 测试GitFox独立版已是4.4时的升级版本列表
r($upgradeZen->getUpgradeVersionsTest('22_7', '22_7', 'gitfox4_4', 'gitfox'))                     && p() && e('gitfox4_3,gitfox4_4'); // 测试GitFox独立版从不低于4.4映射版本的开源版升级时的目标列表
r($upgradeZen->getUpgradeVersionsErrorTest('gitfox4_3', '22_7', '22_6', 'gitfox'))               && p() && e('不支持降级升级：升级目标版本不能低于当前独立版的基线版本。'); // 测试GitFox独立版当前基线低于已安装版本时记录降级错误
r($upgradeZen->getUpgradeVersionsErrorTest('gitfox4_3', 'gitfox4_3', 'gitfox4_4', 'gitfox'))      && p() && e('0');             // 测试GitFox独立版从4.3升级到4.4时不记录错误
