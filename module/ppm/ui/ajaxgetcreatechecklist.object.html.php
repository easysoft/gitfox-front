<?php
declare(strict_types=1);
namespace zin;

unset($config->ppm->createCheck->linkObject->dtable->fieldList['assignedTo']);

$branchParams = "repoID={$repoID}&sourceBranch=" . helper::safe64Encode($sourceBranch) . "&targetBranch=" . helper::safe64Encode($targetBranch);
$pagerLink    = createLink('ppm', 'ajaxGetCreateCheckList', $branchParams . '&recPerPage={recPerPage}&pageID={page}&tab=object');

$tabContent = dtable
(
    set::cols($config->ppm->createCheck->linkObject->dtable->fieldList),
    set::data($objects),
    set::userMap($users),
    set::loadPartial(true),
    set::onRenderCell(jsRaw('window.renderObjectCell')),
    set::footPager(usePager('objectPager', '', array('recPerPage' => $objectPager->recPerPage, 'recTotal' => $objectPager->recTotal, 'linkCreator' => $pagerLink)))
);
