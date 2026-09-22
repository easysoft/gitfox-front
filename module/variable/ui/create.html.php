<?php
declare(strict_types=1);
/**
 * The create view file of variable module of ZenTaoPMS.
 * @copyright   Copyright 2009-2026 禅道软件（青岛）有限公司(ZenTao Software (Qingdao) Co., Ltd. www.zentao.net)
 * @license     ZPL(https://zpl.pub/page/zplv12.html) or AGPL(https://www.gnu.org/licenses/agpl-3.0.en.html)
 * @author      Yang Li <liyang@chandao.com>
 * @package     variable
 * @link        https://www.zentao.net
 */
namespace zin;
$fields = defineFieldList('variable');
$fields->field('name')->required(true)->placeholder($lang->variable->placeholder->name)->width('1/2')->wrapAfter(true);
$fields->field('type')->required(true)->control('picker')->items($lang->variable->typeList)->value($type)->width('1/2')->wrapAfter(true);
$fields->field('key')->required(true)->placeholder($lang->variable->placeholder->key)->width('1/2')->wrapAfter(true);
$fields->field('value')->required(true)->placeholder($lang->variable->placeholder->value)->width('full');
if($type == 'cipher')
{
    $fields->field('value')->control(array('control' => 'input', 'type' => 'password'));
}
else
{
    $fields->field('value')->control(array('control' => 'textarea', 'rows' => 3));
}

$fields->field('desc')->control(array('control' => 'textarea', 'rows' => 3))->width('full');

$fields->autoLoad('type', 'value');

formGridPanel
(
    setID('createForm'),
    set::modeSwitcher(false),
    set::title($title),
    set::labelWidth($app->clientLang == 'zh-cn' ? '6em' : '10em'),
    set::fields($fields),
    set::loadUrl(createLink('variable', 'create', "type={type}"))
);
