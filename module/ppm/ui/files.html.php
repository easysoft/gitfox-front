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

jsVar('ppm', $ppm);

$app->loadLang('codescan');
$app->loadLang('pipeline');
unset($lang->codescan->typeList['all']);
unset($lang->codescan->severityList['all']);

$issueTypes = array();
foreach($lang->codescan->typeList as $issueType => $typeName) $issueTypes[] = array('value' => $issueType, 'text' => $typeName, 'key' => $typeName);
$severities = array();
foreach($lang->codescan->severityList as $severityID => $severityName) $severities[] = array('value' => $severityID, 'text' => $severityName, 'key' => $severityName);
$statusList = array();
foreach($lang->codescan->issueStatusList as $status => $statusName) $statusList[] = array('value' => $status, 'text' => $statusName, 'key' => $statusName);

$domBox = div
(
    zui::diffHub
    (
        set::diffUrl($this->createLink('repo', 'ajaxGetDiffs', "repoID={$repoID}&fromVersion={$fromRevision}&toVersion={$toRevision}")),
        set::enableReview(true),
        set::showAnnotations(true),
        set::enableAnnotations(true),
        set::product(0),
        set::module(''),
        set::issueTypes($issueTypes),
        set::severities($severities),
        set::statusList($statusList),
        set::repoID($repoID),
        set::ppmID($ppm->id),
        set::height('calc(100vh - 286px)'),
        set::labels($lang->pipeline->diffHub->labels),
        set::fileCollapseRules($config->devops->diffConfig),
        set::resolveFileLink(jsRaw('fileDiff => window.resolveFileLink ? window.resolveFileLink(fileDiff) : undefined')),
        set::resolveFileMenu(jsRaw('fileDiff => window.resolveFileMenu ? window.resolveFileMenu(fileDiff) : undefined'))
    )
);
