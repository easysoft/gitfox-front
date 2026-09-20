<?php
declare(strict_types=1);
/**
 * The bug view file of ppm module of ZenTaoPMS.
 * @copyright   Copyright 2009-2025 禅道软件（青岛）有限公司(ZenTao Software (Qingdao) Co., Ltd. www.zentao.net)
 * @license     ZPL(https://zpl.pub/page/zplv12.html) or AGPL(https://www.gnu.org/licenses/agpl-3.0.en.html)
 * @author      Yang Li <liyang@chandao.com>
 * @package     ppm
 * @link        https://www.zentao.net
 */
namespace zin;

$aiIssueCount     = (int)data('aiIssueCount');
$scanIssueCount   = (int)data('staticIssueTotal');
$manualIssueCount = (int)data('bugsTotal');

$issueStatus = (string)data('issueStatus');

$buildIssueStatusBar = function(string $tabKey) use ($ppm, $lang, $config, $issueStatus)
{
    $items = array();
    foreach(zget($config->ppm->issueStatusList, $tabKey, array()) as $statusKey)
    {
        $items[] = array
        (
            'text'   => zget($lang->ppm->issueStatusTabs, $statusKey, $statusKey),
            'active' => $issueStatus === $statusKey,
            'url'    => createLink('ppm', 'view', "id={$ppm->id}&type=bug&param=&recTotal=0&recPerPage=20&pageID=1&issueTab={$tabKey}&issueStatus={$statusKey}")
        );
    }
    return nav(setClass('mb-2'), set::items($items));
};

$scanTabItems = array();
if(!empty($lang->ppm->aiScanIssueList))       $scanTabItems['ai']     = array('text' => "{$lang->ppm->aiScanIssueList} ({$aiIssueCount})",       'url' => 'javascript:;', 'props' => array('data-tab' => 'ai',     'data-target' => '#aiBugsPane'));
if(!empty($lang->ppm->staticScanIssueList))   $scanTabItems['scan']   = array('text' => "{$lang->ppm->staticScanIssueList} ({$scanIssueCount})",   'url' => 'javascript:;', 'props' => array('data-tab' => 'scan',   'data-target' => '#staticScanPane'));
if(!empty($lang->ppm->manualReviewIssueList)) $scanTabItems['static'] = array('text' => "{$lang->ppm->manualReviewIssueList} ({$manualIssueCount})", 'url' => 'javascript:;', 'props' => array('data-tab' => 'static', 'data-target' => '#staticBugs'));

$currentIssueTab = (string)(data('issueTab') ?: zget($_GET, 'issueTab', ''));
$isFirst = true;
foreach($scanTabItems as $tabKey => $tabItem)
{
    $scanTabItems[$tabKey]['active'] = $currentIssueTab ? ($tabKey === $currentIssueTab) : $isFirst;
    $isFirst = false;
}

$staticRows = array();
foreach((array)$staticIssues as $staticIssue)
{
    $staticIssue->repoID = (int)zget($staticIssue, 'repoID', 0) ?: (int)$ppm->targetRepoID;
    $staticRows[] = $staticIssue;
}
$staticCols = $this->loadModel('datatable')->getSetting('codescan', 'issue');
$staticCols['content']['name'] = 'title';
if(hasPriv('codescan', 'issueview')) $staticCols['content']['link'] = array('module' => 'codescan', 'method' => 'issueview', 'params' => 'issueID={id}&repoID={repoID}');
$staticRows = initTableData($staticRows, $staticCols, $this->loadModel('codescan'));

$staticPanel = div
(
    setID('staticScanPane'),
    setStyle(array('display' => 'none')),
    $buildIssueStatusBar('scan'),
    dtable
    (
        setID('staticScanIssues'),
        set::customCols(false),
        set::cols($staticCols),
        set::data($staticRows),
        set::responsive(true),
        set::footPager(usePager('scanPager', '', array
        (
            'recPerPage'  => $scanPager->recPerPage,
            'recTotal'    => $scanPager->recTotal,
            'linkCreator' => createLink('ppm', 'view', "id={$ppm->id}&type=bug&param=&recTotal={$scanPager->recTotal}&recPerPage={recPerPage}&pageID={page}&issueTab=scan&issueStatus={$issueStatus}")
        ), null, '', 'scan'))
    )
);

$manualPanel = div
(
    setID('staticBugs'),
    $buildIssueStatusBar('static'),
    dtable
    (
        set::id('bugs'),
        set::cols($config->ppm->bug->dtable->fieldList),
        set::data(array_values($bugs)),
        set::footPager(usePager('bugPager', '', array('recPerPage' => $bugPager->recPerPage, 'recTotal' => $bugPager->recTotal, 'linkCreator' => createLink('ppm', 'view', "id={$ppm->id}&type=bug&param=&recTotal={$bugPager->recTotal}&recPerPage={recPerPage}&pageID={page}&issueTab=static&issueStatus={$issueStatus}")), null, '', 'static'))
    )
);

$domBox = div
(
    setClass('flex gap-4'),
    sidebar
    (
        set::width(100),
        set::minWidth(100),
        nav
        (
            setID('scanTabs'),
            set::stacked(true),
            set::compact(),
            set::items(array_values($scanTabItems))
        )
    ),
    div(setID('scanPanes'), setClass('flex-auto min-w-0'), $staticPanel, $manualPanel)
);
