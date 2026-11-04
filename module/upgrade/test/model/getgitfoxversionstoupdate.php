#!/usr/bin/env php
<?php
declare(strict_types=1);

/**

title=测试 upgradeModel->getGitfoxVersionsToUpdate();
timeout=0
cid=0

- 测试非GitFox来源优先使用来源版本第一个GitFox版本 @gitfox4_3,gitfox4_4,gitfox4_5,gitfox4_6

- 测试来源版本无GitFox时使用上一开源版本最后GitFox版本 @gitfox4_2,gitfox4_3,gitfox4_4,gitfox4_5,gitfox4_6

- 测试来源和上一版本无GitFox时继续向上查找 @gitfox4_1,gitfox4_2,gitfox4_3,gitfox4_4,gitfox4_5,gitfox4_6
- 测试向前三个版本找不到GitFox时使用升级区间第一个GitFox版本 @gitfox4_1,gitfox4_2,gitfox4_3,gitfox4_4,gitfox4_5,gitfox4_6
- 测试从更早版本升级时使用升级区间第一个GitFox版本 @gitfox4_3,gitfox4_4,gitfox4_5
- 测试GitFox来源使用已安装版本作为起点 @gitfox4_3,gitfox4_4,gitfox4_5,gitfox4_6

- 测试源目标GitFox版本相同时不返回候选版本 @0
- 测试整个区间没有GitFox版本时返回空 @0
- 测试目标GitFox版本使用目标包配置 @gitfox4_3,gitfox4_4,gitfox4_5
- 测试GitFox来源升级到目标包配置的GitFox版本 @gitfox4_4,gitfox4_5
- 测试目标版本无GitFox映射时使用升级区间最后一个GitFox版本 @gitfox4_3,gitfox4_4,gitfox4_5

*/

include dirname(__FILE__, 5) . '/test/lib/init.php';
include dirname(__FILE__, 2) . '/lib/model.class.php';

zenData('user')->loadYaml('user')->gen(5);

su('admin');

$upgrade = new upgradeModelTest();

$openVersions = array(
    '22_4' => '22.4',
    '22_5' => '22.5',
    '22_6' => '22.6',
    '22_7' => '22.7',
    '22_8' => '22.8',
    '22_9' => '22.9',
    '23_0' => '23.0',
    '23_1' => '23.1',
);

$withSource = array(
    'gitfox4_2' => '22_6',
    'gitfox4_3' => '22_7',
    'gitfox4_4' => '22_7',
    'gitfox4_5' => '23_0',
    'gitfox4_6' => '23_0',
    'gitfox4_7' => '23_0',
);

$withoutSource = array(
    'gitfox4_2' => '22_6',
    'gitfox4_3' => '23_0',
    'gitfox4_4' => '23_0',
    'gitfox4_5' => '23_0',
    'gitfox4_6' => '23_0',
    'gitfox4_7' => '23_0',
);

$thirdSearch = array(
    'gitfox4_1' => '22_5',
    'gitfox4_2' => '23_0',
    'gitfox4_3' => '23_0',
    'gitfox4_4' => '23_0',
    'gitfox4_5' => '23_0',
    'gitfox4_6' => '23_0',
    'gitfox4_7' => '23_0',
);

$outsideSearchLimit = array(
    'gitfox4_0' => '22_4',
    'gitfox4_1' => '23_0',
    'gitfox4_2' => '23_0',
    'gitfox4_3' => '23_0',
    'gitfox4_4' => '23_0',
    'gitfox4_5' => '23_0',
    'gitfox4_6' => '23_0',
    'gitfox4_7' => '23_0',
);

$crossVersion = array(
    'gitfox4_3' => '22_7',
    'gitfox4_4' => '22_7',
    'gitfox4_5' => '23_0',
    'gitfox4_6' => '23_0',
);

$withoutTarget = array(
    'gitfox4_2' => '22_6',
    'gitfox4_3' => '22_7',
    'gitfox4_4' => '22_7',
    'gitfox4_5' => '22_8',
    'gitfox4_6' => '22_9',
    'gitfox4_7' => '23_1',
);

r($upgrade->getGitfoxVersionsToUpdateTest('22_7', '23_0', $withSource, $openVersions))         && p() && e('gitfox4_3,gitfox4_4,gitfox4_5,gitfox4_6'); // 测试非GitFox来源优先使用来源版本第一个GitFox版本
r($upgrade->getGitfoxVersionsToUpdateTest('22_7', '23_0', $withoutSource, $openVersions))      && p() && e('gitfox4_2,gitfox4_3,gitfox4_4,gitfox4_5,gitfox4_6'); // 测试来源版本无GitFox时使用上一开源版本最后GitFox版本
r($upgrade->getGitfoxVersionsToUpdateTest('22_7', '23_0', $thirdSearch, $openVersions))        && p() && e('gitfox4_1,gitfox4_2,gitfox4_3,gitfox4_4,gitfox4_5,gitfox4_6'); // 测试来源和上一版本无GitFox时继续向上查找
r($upgrade->getGitfoxVersionsToUpdateTest('22_7', '23_0', $outsideSearchLimit, $openVersions)) && p() && e('gitfox4_1,gitfox4_2,gitfox4_3,gitfox4_4,gitfox4_5,gitfox4_6'); // 测试向前三个版本找不到GitFox时使用升级区间第一个GitFox版本
r($upgrade->getGitfoxVersionsToUpdateTest('22_5', '23_0', $crossVersion, $openVersions))       && p() && e('gitfox4_3,gitfox4_4,gitfox4_5');                     // 测试从更早版本升级时使用升级区间第一个GitFox版本
r($upgrade->getGitfoxVersionsToUpdateTest('gitfox4_3', 'gitfox4_7', $withSource))              && p() && e('gitfox4_3,gitfox4_4,gitfox4_5,gitfox4_6');      // 测试GitFox来源使用已安装版本作为起点
r($upgrade->getGitfoxVersionsToUpdateTest('gitfox4_4', 'gitfox4_4'))                            && p() && e('0');                                              // 测试源目标GitFox版本相同时不返回候选版本
r($upgrade->getGitfoxVersionsToUpdateTest('22_7', '23_0', array(), $openVersions))              && p() && e('0');                                              // 测试整个区间没有GitFox版本时返回空
r($upgrade->getGitfoxVersionsToUpdateTest('22_7', '23_0', $withSource, $openVersions, '4.6', '23_0')) && p() && e('gitfox4_3,gitfox4_4,gitfox4_5');          // 测试目标GitFox版本使用目标包配置
r($upgrade->getGitfoxVersionsToUpdateTest('gitfox4_4', 'gitfox4_6', $withSource, null, '4.6'))  && p() && e('gitfox4_4,gitfox4_5');                           // 测试GitFox来源升级到目标包配置的GitFox版本
r($upgrade->getGitfoxVersionsToUpdateTest('22_7', '23_0', $withoutTarget, $openVersions))        && p() && e('gitfox4_3,gitfox4_4,gitfox4_5');                  // 测试目标版本无GitFox映射时使用升级区间最后一个GitFox版本
