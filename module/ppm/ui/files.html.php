<?php
declare(strict_types=1);
/**
 * The files view file of ppm module of ZenTaoPMS.
 * @copyright   Copyright 2009-2025 禅道软件（青岛）有限公司(ZenTao Software (Qingdao) Co., Ltd. www.zentao.net)
 * @license     ZPL(https://zpl.pub/page/zplv12.html) or AGPL(https://www.gnu.org/licenses/agpl-3.0.en.html)
 * @author      Yang Li <liyang@chandao.com>
 * @package     ppm
 * @link        https://www.zentao.net
 */
namespace zin;
$app->loadLang('codescan');
unset($lang->codescan->typeList['all']);
unset($lang->codescan->severityList['all']);

$issueTypes = array();
foreach($lang->codescan->typeList as $type => $typeName) $issueTypes[] = array('value' => $type, 'text' => $typeName, 'key' => $typeName);
$severities = array();
foreach($lang->codescan->severityList as $severityID => $severityName) $severities[] = array('value' => $severityID, 'text' => $severityName, 'key' => $severityName);

$domBox = div
(
    zui::diffHub
    (
        set::diffUrl($this->createLink('repo', 'ajaxGetDiffs', "repoID={$repoID}&fromVersion={$fromRevision}&toVersion={$toRevision}")),
        set::enableAnnotations(true),
        set::enableReview(true),
        set::product(0),
        set::module(''),
        set::issueTypes($issueTypes),
        set::severities($severities),
        set::repoID($repoID),
        set::ppmID($ppm->id),
        set::hight('calc(100vh - 286px)'),
        set::labels($lang->ppm->diffHub->labels),
        set::fileCollapseRules($config->devops->diffConfig),
    )
);
