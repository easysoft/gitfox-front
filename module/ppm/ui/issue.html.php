<?php
declare(strict_types=1);
/**
 * The issue view file of ppm module of ZenTaoPMS.
 * @copyright   Copyright 2009-2025 禅道软件（青岛）有限公司(ZenTao Software (Qingdao) Co., Ltd. www.zentao.net)
 * @license     ZPL(https://zpl.pub/page/zplv12.html) or AGPL(https://www.gnu.org/licenses/agpl-3.0.en.html)
 * @author      Yang Li <liyang@chandao.com>
 * @package     ppm
 * @link        https://www.zentao.net
 */
namespace zin;

$issueTab          = (string)data('issueTab');
$issueStatus       = (string)data('issueStatus');
$issuePager        = data('issuePager');
$issues            = data('issues');
$issueStatusMenu   = (array)data('issueStatusMenu');
$issueStatusLink   = (string)data('issueStatusLink');
$issuePagerOptions = (array)data('issuePagerOptions');
if(!is_array($issues)) $issues = array();

$app->loadLang('codescan');
$app->loadConfig('codescan');

$tabItems = array();
foreach((array)data('issueTabs') as $tabKey => $tabName)
{
    $statusList = zget($config->ppm->issueStatusList, $tabKey, array());
    $tabStatus  = in_array($issueStatus, $statusList) ? $issueStatus : 'wait';
    $tabItems[] = array
    (
        'text'   => $tabName,
        'active' => $tabKey === $issueTab,
        'url'    => createLink('ppm', 'view', "id={$ppm->id}&type=issue&param=&recTotal=0&recPerPage=20&pageID=1&issueTab={$tabKey}&issueStatus={$tabStatus}")
    );
}

$issuePanel = null;
if(in_array($issueTab, array('static', 'manual')))
{
    $batchConfig = $issueTab == 'manual' ? $config->codescan->manualIssue : $config->codescan->issue;
    jsVar('window.batchStatus',   $batchConfig->batchStatus);
    jsVar('window.batchBlockers', $batchConfig->batchBlockers);

    $batchButtons = $batchConfig->batchList;
    if(isset($batchButtons['bug']))
    {
        $batchRepoID  = (int)$ppm->targetRepoID;
        $batchRepo    = $batchRepoID ? $this->loadModel('repo')->getByID($batchRepoID) : null;
        $batchProduct = empty($batchRepo->product) ? 0 : (int)explode(',', (string)$batchRepo->product)[0];
        $batchButtons['bug']['data-url'] = createLink('bug', 'batchCreate', "productID={$batchProduct}&branch=0&executionID=0&moduleID=0&extra=from=codescan,fromServerID={$batchRepoID},repoID={$batchRepoID}");
    }

    $batchItems = array();
    foreach(zget((array)$batchConfig->batchMenu, $issueStatus, array()) as $action)
    {
        if(empty($batchButtons[$action])) continue;

        $button = $batchButtons[$action];
        $priv   = zget($button, 'priv', array('codescan', $action));
        if(!hasPriv($priv[0], $priv[1])) continue;

        unset($button['priv']);
        $batchItems[] = $button;
    }
    $batchToolbar = empty($batchItems) ? null : array('items' => $batchItems);

    $lang->ppm->featureBar['issueStatus'] = $issueStatusMenu;

    $issueRows = array_values($issues);
    if($issueTab == 'static')
    {
        foreach($issueRows as $issue) $issue->repoID = (int)zget($issue, 'repoID', 0) ?: (int)$ppm->targetRepoID;

        $issueCols = $this->loadModel('datatable')->getSetting('codescan', 'issue');
        $issueCols['content']['name'] = 'title';
        if(hasPriv('codescan', 'issueview')) $issueCols['content']['link'] = array('module' => 'codescan', 'method' => 'issueview', 'params' => 'issueID={id}&repoID={repoID}');
        $dtableID = 'staticScanIssues';
    }
    else
    {
        $issueCols = $config->codescan->manualIssue->dtable->fieldList;
        $dtableID  = 'bugs';
    }

    $issueRows = initTableData($issueRows, $issueCols, $this->loadModel('codescan'));
    $issuePanel = div
    (
        featureBar
        (
            set::module('ppm'),
            set::method('issueStatus'),
            set::current($issueStatus),
            set::linkParams($issueStatusLink),
            set::labelCount((string)$issuePager->recTotal)
        ),
        dtable
        (
            setID($dtableID),
            set::customCols(false),
            set::cols($issueCols),
            set::data($issueRows),
            set::responsive(true),
            $batchToolbar ? set::checkable(true) : null,
            set::footToolbar($batchToolbar),
            set::footPager(usePager('issuePager', '', $issuePagerOptions, null, '', $issueTab))
        )
    );
}

$domBox = div
(
    setClass('flex gap-4'),
    sidebar
    (
        set::width(100),
        set::minWidth(100),
        nav
        (
            setID('issueTabs'),
            set::stacked(true),
            set::compact(),
            set::items($tabItems)
        )
    ),
    div(setID('issuePanes'), setClass('flex-auto min-w-0'), $issuePanel)
);
