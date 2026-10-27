<?php
/**
 * The repo entry point of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2023 禅道软件（青岛）有限公司(ZenTao Software (Qingdao) Co., Ltd. www.cnezsoft.com)
 * @license     ZPL(http://zpl.pub/page/zplv12.html) or AGPL(https://www.gnu.org/licenses/agpl-3.0.en.html)
 * @author      xiawenlong <xiawenlong@cnezsoft.com>
 * @package     repo
 * @version     1
 * @link        https://www.zentao.net
 */
class gitlabWebhookEntry extends baseEntry
{

    /**
     * 获取请求头值，头名不区分大小写。
     *
     * @param  array  $headers
     * @param  string $name
     * @access private
     * @return string
     */
    private function getHeaderValue(array $headers, string $name): string
    {
        foreach($headers as $key => $value)
        {
            if(strcasecmp($key, $name) === 0) return (string)$value;
        }

        return '';
    }

    /**
     * Repo webhook.
     *
     * @access public
     * @return string
     */
    public function post()
    {
        $headers = getallheaders(); /* Fetch all HTTP request headers. */
        $event   = $this->getHeaderValue($headers, 'X-Gitlab-Event');
        if(empty($event)) return;

        $token = $this->getHeaderValue($headers, 'X-Gitlab-Token');

        /* 流水线 webhook：保持原有逻辑，使用 entry(gitfox) 的 key 校验。 */
        $pipelineID = $this->param('pipelineID');
        if(!empty($pipelineID))
        {
            $entry    = $this->loadModel('entry')->getByCode('gitfox');
            $entryKey = empty($entry) ? '' : (string)$entry->key;
            if(empty($entryKey) || !hash_equals($entryKey, (string)$token)) return $this->sendError(401, 'Unauthorized');

            $pipeline = $this->loadModel('pipeline')->getByID((int)$pipelineID);
            if(empty($pipeline)) return;
            if($pipeline->engine != 'gitlab') return;

            $this->pipeline->handleWebhook($event, $this->requestBody, $pipeline);
            return;
        }

        /* 代码库 webhook（镜像库）：使用代码库自己的 gitUID 作为 token 校验。 */
        $repoID = $this->param('repoID');
        if(empty($repoID)) return;

        $repo = $this->loadModel('repo')->fetchByID((int)$repoID);
        if(empty($repo)) return;

        /* 迁移仓库的 gitUID 是 empty_gituid_{repoID} 占位值，可被推算，不能作为密钥。 */
        $expectedToken = isset($repo->gitUID) ? (string)$repo->gitUID : '';
        if(empty($expectedToken) || strpos($expectedToken, 'empty_gituid_') === 0 || !hash_equals($expectedToken, (string)$token)) return $this->sendError(401, 'Unauthorized');

        /* 设置匿名用户，供 handleWebhook 内部创建 action、commit 记录使用。 */
        $this->app->user = new stdclass();
        $this->app->user->account = '';
        $this->app->user->admin   = false;
        $this->app->user->rights['rights'] = array();
        $this->app->user->rights['acls']   = array();

        /* fetchByID 不填充 gitfox 连接信息，这里参照 gitfoxwebhook.php 补齐。 */
        $server = $this->loadModel('gitfox')->getServer();
        if(empty($server)) return;

        $repo->apiPath  = sprintf($this->config->repo->gitfox->apiPath, $server->url, $repo->id);
        $repo->client   = $server->url;
        $repo->password = $server->token;

        $this->loadController('user', 'login');
        $this->repo->handleWebhook($event, $this->requestBody, $repo);
    }
}
