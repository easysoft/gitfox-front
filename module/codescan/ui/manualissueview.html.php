<?php
declare(strict_types=1);
/**
 * The manual review issue view file of codescan module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2026 禅道软件（青岛）集团有限公司(ZenTao Software (Qingdao) Co., Ltd. www.zentao.net)
 * @license     ZPL(https://zpl.pub/page/zplv12.html) or AGPL(https://www.gnu.org/licenses/agpl-3.0.en.html)
 * @package     codescan
 * @link        https://www.zentao.net
 */
namespace zin;

$isInModal = isInModal();
if(!empty($repoID) && !$isInModal) dropmenu
(
    set::objectID($repoID),
    set::tab('repo'),
    set::module('repo'),
    set::method('review'),
    set::url(createLink('repo', 'ajaxGetDropMenu', "repoID={$repoID}&module=repo&method=review")),
    set::cache(false)
);

$app->loadConfig('codescan');

$manualCols  = $config->codescan->manualIssue->dtable->fieldList;
$typeName    = zget(zget($manualCols['type'], 'map', array()), $issue->type, $issue->type);
$levelName   = zget(zget($manualCols['priority'], 'map', array()), $issue->priority, $issue->priority);
$statusName  = zget($lang->codescan->issueStatusList, $issue->status, $issue->status);
$levelColor  = zget(array('critical' => 'danger-pale', 'high' => 'danger-pale', 'medium' => 'warning-pale', 'low' => 'primary-pale'), $issue->priority, 'gray-500-pale');
$statusColor = in_array($issue->status, array('ignore', 'closed')) ? 'gray-500-pale' : 'primary-pale';

detailHeader
(
    to::prefix
    (
        backBtn
        (
            set::icon('back'),
            set::type('secondary'),
            set::back('ppm-view,repo-review'),
            $lang->goback
        ),
        label(setClass('flex-none'), $issue->id),
        entityLabel
        (
            set::level(1),
            set::text($issue->title)
        ),
        label(setClass("flex-none size-lg {$levelColor}"), $levelName),
        label(setClass('flex-none size-lg gray-500-pale'), $typeName),
        label(setClass("flex-none size-lg {$statusColor}"), $statusName)
    )
);

$actionList = $this->loadModel('common')->buildOperateMenu($issue, 'codescan');

div
(
    setClass('detail-body row gap-2 items-start'),
    div
    (
        setClass('detail-main flex-auto col gap-2 min-w-0'),
        sectionList
        (
            setID('basicInfo'),
            set::title($lang->basicInfo),
            div
            (
                h4(setClass('font-bold mb-2'), $lang->codescan->issueLocation),
                div(setClass('text'), "{$issue->file} {$issue->lineRange}")
            ),
            div
            (
                setClass('mt-2'),
                h4(setClass('font-bold mb-2'), $lang->codescan->issueReason),
                div(setClass('article pre-wrap text'), html($issue->content))
            )
        ),
        div
        (
            setClass('detail-actions center sticky mt-4 bottom-4 z-10'),
            floatToolbar
            (
                set::object($issue),
                to::prefix(backBtn(set::icon('back'), set::back('ppm-view,repo-review'), $lang->goback)),
                empty($actionList['mainActions']) ? null : set::main($actionList['mainActions'])
            )
        )
    ),
    div
    (
        setClass('detail-side flex-none relative'),
        setStyle('width', '370px'),
        empty($ppm) ? null : div
        (
            setClass('canvas py-1 px-2 mb-4'),
            h4(setClass('font-bold mb-2'), $lang->ppm->common),
            div
            (
                span($lang->ppm->common . '：'),
                a
                (
                    setClass('text-primary'),
                    set::href(createLink('ppm', 'view', "id={$issue->ppmID}&type=basic")),
                    "#{$issue->ppmID} {$ppm->title}"
                )
            )
        ),
        history
        (
            setClass('mt-4 border-0 canvas'),
            setStyle(array('box-shadow' => 'var(--shadow-none)')),
            set::objectType('codescanissue'),
            set::objectID($issueID),
            set::commentUrl(createLink('action', 'comment', array('objectType' => 'codescanissue', 'objectID' => $issueID)))
        )
    )
);
