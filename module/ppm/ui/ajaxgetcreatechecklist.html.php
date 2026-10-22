<?php
declare(strict_types=1);
namespace zin;
global $app;

$app->loadLang('pipeline');

$tabContent = null;
include "ajaxgetcreatechecklist.{$tab}.html.php";

$tabLink = createLink('ppm', 'ajaxGetCreateCheckList', "repoID={$repoID}&sourceBranch=" . helper::safe64Encode($sourceBranch) . "&targetBranch=" . helper::safe64Encode($targetBranch) . '&recPerPage=20&pageID=1&tab=%s');

$tabTitles = array
(
    'commit' => $lang->ppm->commitLogs,
    'diff'   => $lang->ppm->changeFiles
);
if($config->edition != 'gitfox') $tabTitles['object'] = $lang->ppm->linkedObject;

$tabPanes = array();
foreach($tabTitles as $tabKey => $tabTitle)
{
    $tabPanes[] = tabPane
    (
        set::key($tabKey),
        set::title($tabTitle),
        set::active($tab == $tabKey),
        set::url(sprintf($tabLink, $tabKey)),
        $tab == $tabKey ? $tabContent : null
    );
}

tabs(setID('createCheckListTabs'), $tabPanes);
