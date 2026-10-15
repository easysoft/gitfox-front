<?php
declare(strict_types=1);
/**
 * The review view file of repo module of ZenTaoPMS.
 * @copyright   Copyright 2009-2026 禅道软件（青岛）集团有限公司(ZenTao Software (Qingdao) Co., Ltd. www.zentao.net)
 * @license     ZPL(https://zpl.pub/page/zplv12.html) or AGPL(https://www.gnu.org/licenses/agpl-3.0.en.html)
 * @author      Zeng Gang<zenggang@easycorp.ltd>
 * @package     repo
 * @link        https://www.zentao.net
 */
namespace zin;

$module = $app->tab == 'devops' ? 'repo' : $app->tab;

$devopsAllRepos = common::isDevopsHomeMenuPage();
$showRepoFilter = ($config->vision == 'gitfox' && $app->tab == 'issue') || $devopsAllRepos;
$allRepoParam   = $devopsAllRepos ? '&allRepo=1' : '';

if(!$allRepo) dropmenu
(
    set::module($module),
    set::tab($module),
    set::url(createLink($module, 'ajaxGetDropMenu', "objectID=$objectID&module={$app->rawModule}&method={$app->rawMethod}"))
);

jsVar('appTab', $app->tab);
jsVar('window.batchStatus',   $config->codescan->manualIssue->batchStatus);
jsVar('window.batchBlockers', $config->codescan->manualIssue->batchBlockers);

$app->loadLang('codescan');
$app->loadConfig('codescan');

$batchButtons = $config->codescan->manualIssue->batchList;
if(isset($batchButtons['bug']))
{
    $batchProductID = empty($repo->product) ? 0 : (int)explode(',', (string)$repo->product)[0];
    $batchButtons['bug']['data-url'] = createLink('bug', 'batchCreate', "productID={$batchProductID}&branch=0&executionID=0&moduleID=0&extra=from=codescan,fromServerID={$repoID},repoID={$repoID}");
}
$footToolbar = array('items' => array());
foreach(zget((array)$config->codescan->manualIssue->batchMenu, $browseType, array()) as $action)
{
    if(empty($batchButtons[$action])) continue;

    $button = $batchButtons[$action];
    $priv   = zget($button, 'priv', array('codescan', $action));
    if(!hasPriv($priv[0], $priv[1])) continue;

    unset($button['priv']);
    $footToolbar['items'][] = $button;
}
if(empty($footToolbar['items'])) $footToolbar = null;

// 人工审查问题列表与代码评审问题清单共用列和行数据处理，保证两处口径一致。
$manualCols = $config->codescan->manualIssue->dtable->fieldList;
$manualRows = array();
foreach((array)$manualIssues as $manualIssue)
{
    $manualRows[] = $this->loadModel('codescan')->processManualIssueData($manualIssue, (int)$repoID);
}
$manualRows = initTableData($manualRows, $manualCols, $this->loadModel('codescan'));

// 状态筛选与代码评审问题清单中的人工审查问题保持一致。
$statusItems = array();
$statusList = array
(
    'all'       => $lang->all,
    'wait'      => zget($lang->codescan->issueStatusList, 'wait', 'wait'),
    'solved'    => zget($lang->codescan->issueStatusList, 'solved', 'solved'),
    'closed'    => zget($lang->codescan->issueStatusList, 'closed', 'closed'),
    'converted' => $lang->codescan->manualIssueConverted
);
foreach($statusList as $status => $statusText)
{
    $statusItems[] = array
    (
        'text'   => $statusText,
        'active' => $browseType === $status,
        'badge'  => $browseType === $status && $pager->recTotal != '' ? array('text' => $pager->recTotal, 'class' => 'size-sm canvas ring-0 rounded-md') : null,
        'props'  => array('data-id' => $status, 'data-load' => 'table', 'title' => $statusText),
        'url'    => createLink('repo', 'review', "repoID={$repoID}&browseType={$status}&objectID={$objectID}&orderBy={$orderBy}&recTotal=0&recPerPage=20&pageID=1{$allRepoParam}")
    );
}

/* 代码库筛选下拉。The repo filter dropdown. */
$repoItems = array();
if($showRepoFilter)
{
    $repoItems[] = array('text' => $lang->repo->allRepo, 'data-app' => $app->tab, 'url' => createLink('repo', 'review', "repoID=0&browseType={$browseType}&objectID={$objectID}&orderBy={$orderBy}&recTotal=0&recPerPage=20&pageID=1{$allRepoParam}"));
    foreach($repoPairs as $repoId => $repoName)
    {
        $repoItems[] = array('text' => $repoName, 'data-app' => $app->tab, 'url' => createLink('repo', 'review', "repoID={$repoId}&browseType={$browseType}&objectID={$objectID}&orderBy={$orderBy}&recTotal=0&recPerPage=20&pageID=1{$allRepoParam}"));
    }
}

featureBar
(
    !$showRepoFilter ? null : to::before
    (
        dropdown
        (
            to('trigger', btn(zget($repoPairs, $repoID, $lang->repo->allRepo), setID('repoDropdown'), setClass('ghost text-ellipsis text-left'))),
            set::items($repoItems)
        )
    ),
    set::items($statusItems)
);

dtable
(
    setID('manualIssueTable'),
    set::customCols(false),
    $footToolbar ? set::checkable(true) : null,
    set::cols($manualCols),
    set::data($manualRows),
    set::responsive(true),
    set::footToolbar($footToolbar),
    set::footPager(usePager())
);
