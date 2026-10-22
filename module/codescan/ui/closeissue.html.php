<?php
declare(strict_types=1);
/**
 * The close issue view file of codescan module of ZenTaoPMS.
 * @copyright   Copyright 2009-2025 禅道软件（青岛）有限公司(ZenTao Software (Qingdao) Co., Ltd. www.zentao.net)
 * @license     ZPL(https://zpl.pub/page/zplv12.html) or AGPL(https://www.gnu.org/licenses/agpl-3.0.en.html)
 * @author      ZenTao
 * @package     codescan
 * @link        https://www.zentao.net
 */
namespace zin;

$issueIdList = isset($issueIdList) ? (array)$issueIdList : array();
if(empty($issueIdList) && !empty($_GET['issueIdList'])) $issueIdList = explode(',', (string)$_GET['issueIdList']);

$hiddenInputs = array();
foreach($issueIdList as $issueID) $hiddenInputs[] = formHidden('issueIdList[]', (int)$issueID);

formPanel
(
    set::title($title),
    set::actions(array('submit')),
    formRow
    (
        formGroup
        (
            set::name('reason'),
            set::required(true),
            set::label($lang->codescan->closeReason)
        )
    ),
    ...$hiddenInputs
);
