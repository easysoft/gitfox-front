#!/usr/bin/env php
<?php
declare(strict_types=1);

/**

title=测试 upgradeModel->getGitfoxVersionsToUpdate();
timeout=0
cid=0

- 测试非GitFox来源使用上一开源版本最后GitFox版本 @gitfox4_2,gitfox4_3,gitfox4_4,gitfox4_5,gitfox4_6

- 测试上一开源版本无GitFox时使用来源版本第一个GitFox版本 @gitfox4_3,gitfox4_4,gitfox4_5,gitfox4_6

- 测试来源和上一版本均无GitFox时使用区间第一个版本 @gitfox4_5
- 测试GitFox来源使用已安装版本作为起点 @gitfox4_3,gitfox4_4,gitfox4_5,gitfox4_6

- 测试源目标GitFox版本相同时不返回候选版本 @0
- 测试整个区间没有GitFox版本时返回空 @0

*/

include dirname(__FILE__, 5) . '/test/lib/init.php';
include dirname(__FILE__, 2) . '/lib/model.class.php';

zenData('user')->loadYaml('user')->gen(5);

su('admin');

$upgrade = new upgradeModelTest();

$openVersions = array(
    '22_5' => '22.5',
    '22_6' => '22.6',
    '22_7' => '22.7',
    '23_0' => '23.0',
);

$withPrevious = array(
    'gitfox4_2' => '22_6',
    'gitfox4_3' => '22_7',
    'gitfox4_4' => '22_7',
    'gitfox4_5' => '23_0',
    'gitfox4_6' => '23_0',
    'gitfox4_7' => '23_0',
);

$withoutPrevious = array(
    'gitfox4_3' => '22_7',
    'gitfox4_4' => '22_7',
    'gitfox4_5' => '23_0',
    'gitfox4_6' => '23_0',
    'gitfox4_7' => '23_0',
);

$targetOnly = array(
    'gitfox4_5' => '23_0',
    'gitfox4_6' => '23_0',
);

r($upgrade->getGitfoxVersionsToUpdateTest('22_7', '23_0', $withPrevious, $openVersions))    && p() && e('gitfox4_2,gitfox4_3,gitfox4_4,gitfox4_5,gitfox4_6'); // 测试非GitFox来源使用上一开源版本最后GitFox版本
r($upgrade->getGitfoxVersionsToUpdateTest('22_7', '23_0', $withoutPrevious, $openVersions)) && p() && e('gitfox4_3,gitfox4_4,gitfox4_5,gitfox4_6');      // 测试上一开源版本无GitFox时使用来源版本第一个GitFox版本
r($upgrade->getGitfoxVersionsToUpdateTest('22_7', '23_0', $targetOnly, $openVersions))      && p() && e('gitfox4_5');                                   // 测试来源和上一版本均无GitFox时使用区间第一个版本
r($upgrade->getGitfoxVersionsToUpdateTest('gitfox4_3', 'gitfox4_7', $withPrevious))         && p() && e('gitfox4_3,gitfox4_4,gitfox4_5,gitfox4_6');      // 测试GitFox来源使用已安装版本作为起点
r($upgrade->getGitfoxVersionsToUpdateTest('gitfox4_4', 'gitfox4_4'))                       && p() && e('0');                                              // 测试源目标GitFox版本相同时不返回候选版本
r($upgrade->getGitfoxVersionsToUpdateTest('22_7', '23_0', array(), $openVersions))           && p() && e('0');                                              // 测试整个区间没有GitFox版本时返回空
