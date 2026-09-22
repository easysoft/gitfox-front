<?php
declare(strict_types=1);
/**
 * The zen file of variable module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2026 禅道软件（青岛）有限公司(ZenTao Software (Qingdao) Co., Ltd. www.zentao.net)
 * @license     ZPL(https://zpl.pub/page/zplv12.html) or AGPL(https://www.gnu.org/licenses/agpl-3.0.en.html)
 * @author      Yang Li <liyang@chandao.com>
 * @package     variable
 * @link        https://www.zentao.net
 */
class variableZen extends variable
{
    /**
     * 校验表单数据。
     * Check form data.
     *
     * @param  object $formData
     * @access public
     * @return bool
     */
    public function checkFormData(object $formData): bool
    {
        if(!preg_match('/^[\x{4e00}-\x{9fa5}a-zA-Z0-9\-_]+$/u', (string)$formData->name)) dao::$errors['name'][]  = $this->lang->variable->placeholder->name;
        if(!preg_match('/^[a-zA-Z0-9]+$/', (string)$formData->key))                       dao::$errors['key'][]   = $this->lang->variable->placeholder->key;
        if(!preg_match('/^[a-zA-Z0-9\-_]+$/', (string)$formData->value))                  dao::$errors['value'][] = $this->lang->variable->placeholder->value;

        return !dao::isError();
    }
}
