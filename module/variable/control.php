<?php
declare(strict_types=1);
/**
 * The control file of variable module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2026 禅道软件（青岛）有限公司(ZenTao Software (Qingdao) Co., Ltd. www.zentao.net)
 * @license     ZPL(https://zpl.pub/page/zplv12.html) or AGPL(https://www.gnu.org/licenses/agpl-3.0.en.html)
 * @author      Yang Li <liyang@chandao.com>
 * @package     variable
 * @link        https://www.zentao.net
 */
class variable extends control
{
    public function __construct($module = '', $method = '')
    {
        parent::__construct($module, $method);
        $this->loadModel('space')->setMenu();
    }

    /**
     * 浏览变脸列表。
     * Browse variable list.
     *
     * @param  string $orderBy
     * @param  int    $recPerPage
     * @param  int    $pageID
     * @access public
     * @return void
     */
    public function browse(string $orderBy = 'id_desc', int $recTotal = 0, int $recPerPage = 20, int $pageID = 1)
    {
        $this->app->loadClass('pager', true);
        $pager = new pager($recTotal, $recPerPage, $pageID);

        $this->view->title     = $this->lang->variable->browse;
        $this->view->providers = $this->variable->getList($orderBy, $pager);
        $this->view->orderBy   = $orderBy;
        $this->view->users     = $this->loadModel('user')->getPairs('noletter');
        $this->view->pager     = $pager;
        $this->display();
    }

    /**
     * 创建变量。
     * Create variable.
     *
     * @param  string $type
     * @access public
     * @return void
     */
    public function create(string $type = 'plain')
    {
        if($_POST)
        {
            $formData = form::data($this->config->variable->form->create)
                ->add('createdBy', $this->app->user->account)
                ->skipSpecial('name')
                ->get();
            $this->variableZen->checkFormData($formData);
            if(dao::isError()) return $this->sendError(dao::getError());

            $id = $this->variable->create($formData);
            if(dao::isError()) return $this->sendError(dao::getError());

            if($id) $this->loadModel('action')->create('variable', $id, 'created');

            return $this->sendSuccess(array('load' => $this->createLink('variable', 'browse')));
        }
        $this->view->title = $this->lang->variable->create;
        $this->view->type  = $type;
        $this->display();
    }

    /**
     * 编辑变量。
     * Edit variable.
     *
     * @param  int    $id
     * @param  string $type
     * @access public
     * @return void
     */
    public function edit(int $id, string $type = '')
    {
        $variable = $this->variable->fetchById($id);
        if($_POST)
        {
            $formData = form::data($this->config->variable->form->edit)
                ->add('editedBy', $this->app->user->account)
                ->skipSpecial('name')
                ->get();
            $this->variableZen->checkFormData($formData);
            if(dao::isError()) return $this->sendError(dao::getError());

            $result = $this->variable->update($id, $formData);
            if(dao::isError()) return $this->sendError(dao::getError());

            if($result) $this->loadModel('action')->create('variable', $id, 'edited');

            return $this->sendSuccess(array('load' => $this->createLink('variable', 'browse')));
        }
        $this->view->title    = $this->lang->variable->edit;
        $this->view->type     = $type ? $type : $variable->type;
        $this->view->variable = $variable;
        $this->display();
    }

    /**
     * 删除变量。
     * delete variable.
     *
     * @param  int $id
     * @access public
     * @return void
     */
    public function delete(int $id)
    {
        $this->variable->delete(TABLE_VARIABLE, $id);
        if(dao::isError()) return $this->sendError(dao::getError());
        return $this->sendSuccess(array('message' => $this->lang->deleteSuccess, 'load' => $this->createLink('variable', 'browse')));
    }
}
