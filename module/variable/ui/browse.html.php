<?php
declare(strict_types=1);
/**
 * The browse view file of variable module of ZenTaoPMS.
 * @copyright   Copyright 2009-2026 禅道软件（青岛）有限公司(ZenTao Software (Qingdao) Co., Ltd. www.zentao.net)
 * @license     ZPL(https://zpl.pub/page/zplv12.html) or AGPL(https://www.gnu.org/licenses/agpl-3.0.en.html)
 * @author      Yang Li <liyang@chandao.com>
 * @package     variable
 * @link        https://www.zentao.net
 */
namespace zin;

$createItem = array
(
    'text'     => $lang->variable->create,
    'url'      => createLink('variable', 'create'),
    'icon'     => 'plus',
    'data-app' => $app->tab,
    'class'    => 'primary'
);

featureBar();
toolbar(hasPriv('variable', 'create') ? item(set($createItem)) : null);

$data = initTableData($providers, $config->variable->dtable->fieldList);
dtable
(
    set::cols($config->variable->dtable->fieldList),
    set::data($data),
    set::userMap($users),
    set::orderBy($orderBy),
    set::sortLink(createLink('variable', 'browse', "orderBy={name}_{sortType}&recTotal={$pager->recTotal}&recPerPage={$pager->recPerPage}&pageID={$pager->pageID}")),
    set::footPager(usePager()),
    set::emptyTip($lang->variable->notice->emptyProvider)
);
