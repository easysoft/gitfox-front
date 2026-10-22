<?php
declare(strict_types=1);
namespace zin;

if(!hasPriv('repo', 'diff')) unset($config->ppm->createCheck->commit->dtable->fieldList['id']['link']);

$branchParams = "repoID={$repoID}&sourceBranch=" . helper::safe64Encode($sourceBranch) . "&targetBranch=" . helper::safe64Encode($targetBranch);
$pagerLink    = createLink('ppm', 'ajaxGetCreateCheckList', $branchParams . '&recPerPage={recPerPage}&pageID={page}&tab=commit');

$tabContent = dtable
(
    set::cols($config->ppm->createCheck->commit->dtable->fieldList),
    set::data($commits),
    set::userMap($users),
    set::loadPartial(true),
    set::footPager(usePager('commitPager', '', array('recPerPage' => $commitPager->recPerPage, 'recTotal' => $commitPager->recTotal, 'linkCreator' => $pagerLink)))
);
