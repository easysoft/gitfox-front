<?php
declare(strict_types=1);
/**
 * The model file of variable module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2026 禅道软件（青岛）有限公司(ZenTao Software (Qingdao) Co., Ltd. www.zentao.net)
 * @license     ZPL(https://zpl.pub/page/zplv12.html) or AGPL(https://www.gnu.org/licenses/agpl-3.0.en.html)
 * @author      Yang Li <liyang@chandao.com>
 * @package     variable
 * @link        https://www.zentao.net
 */
class variableModel extends model
{
    /**
     * 获取变量列表。
     * Get variable list.
     *
     * @param  string $orderBy
     * @param  ?object $pager
     * @access public
     * @return array
     */
    public function getList(string $orderBy = 'id_desc', ?object $pager = null): array
    {
        return $this->dao->select('*')->from(TABLE_VARIABLE)
            ->where('deleted')->eq(0)
            ->orderBy($orderBy)
            ->page($pager)
            ->fetchAll('id');
    }

    /**
     * 创建一个变量。
     * Create a variable.
     *
     * @param  object $formData
     * @access public
     * @return int
     */
    public function create(object $formData): int|false
    {
        $repoID  = zget($formData, 'repoID', 0);
        $spaceID = zget($formData, 'spaceID', 0);

        $this->dao->insert(TABLE_VARIABLE)->data($formData)
            ->batchCheck($this->config->variable->create->requiredFields, 'notempty')
            ->check('name', 'unique', "`repoID` = $repoID and `spaceID` = $spaceID and `deleted` = 0")
            ->check('key', 'unique')
            ->autoCheck()
            ->exec();
        if(dao::isError()) return false;

        return $this->dao->lastInsertID();
    }
}
