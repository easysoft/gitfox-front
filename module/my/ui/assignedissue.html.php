<?php
declare(strict_types=1);
/**
 * The assignedissue view file of my module of ZenTaoPMS.
 * @copyright   Copyright 2009-2023 禅道软件（青岛）集团有限公司(ZenTao Software (Qingdao) Co., Ltd. www.zentao.net)
 * @license     ZPL(https://zpl.pub/page/zplv12.html) or AGPL(https://www.gnu.org/licenses/agpl-3.0.en.html)
 * @author      Yang Li <liyang@chandao.com>
 * @package     my
 * @link        https://www.zentao.net
 */
namespace zin;

include 'header.html.php';

/* 待处理下的标签，标签项由 $lang->my->featureBar['work'][$mode] 定义。 */
featureBar
(
    set::current($mode),
    set::linkParams("mode={key}")
);

/* 列表字段与代码评审详情页问题清单中的人工审查问题列表保持一致。 */
$codescanModel = $this->loadModel('codescan');
$cols = $config->codescan->manualIssue->dtable->fieldList;
$cols['title']['data-app'] = 'repo';
$rows = array();
foreach($issues as $issue) $rows[] = $codescanModel->processManualIssueData($issue);
$rows = initTableData($rows, $cols, $codescanModel);

dtable
(
    set::id('issues'),
    set::customCols(false),
    set::responsive(true),
    set::cols(array_values($cols)),
    set::data(array_values($rows)),
    set::footPager(usePager()),
    set::emptyTip($lang->block->emptyTip)
);
