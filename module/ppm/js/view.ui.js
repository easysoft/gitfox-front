/**
 * 调用 SSE 接口并输出返回值
 * Call SSE endpoint and output returned values
 */
function connectSSE() {
    // 如果已存在相同的 SSE 连接，则先关闭它
    if (window.eventSource && window.eventSource.readyState !== EventSource.CLOSED) {
        window.eventSource.close();
        console.log('Existing SSE connection closed');
    }

    // 用于存储 EventSource 实例
    window.eventSource = new EventSource(sseURL);

    // 重连计数器
    let reconnectAttempts = 0;
    const maxReconnectAttempts = 5;

    // 重连间隔时间（毫秒）
    const reconnectInterval = 5000;

    window.eventSource.onopen = function(event) {
        console.log('SSE Connected:', event);
        // 重置重连计数器
        reconnectAttempts = 0;
    };

    // 监听默认消息
    window.eventSource.onmessage = function(event) {
        console.log('Message:', event);
    };

    window.eventSource.addEventListener('pullreq_updated', function(event)
    {
        const eventData = JSON.parse(event.data);
        console.log('pullreq_updated', eventData);
        if(mrID == eventData.id)
        {
            $.getJSON($.createLink('ppm', 'ajaxCheckReviewFlow', 'id=' + mrID));
            loadCurrentPage('#mr-detail');
        }
    });

    window.eventSource.onerror = function(error) {
        console.error('SSE Error:', error);
        if (window.eventSource.readyState === EventSource.CLOSED) {
            console.log('closed', 'Connected closed');

            // 尝试重连
            if (reconnectAttempts < maxReconnectAttempts) {
                reconnectAttempts++;
                console.log(`Attempting to reconnect... (${reconnectAttempts}/${maxReconnectAttempts})`);

                // 使用 setTimeout 实现延迟重连
                setTimeout(() => {
                    connectSSE();
                }, reconnectInterval);
            } else {
                console.log('Max reconnection attempts reached. Giving up.');
            }
        } else {
            console.log('error', 'Connected error, reconnecting...');
        }
    };
}

/**
 * 页面加载后建立 SSE 连接，接收 GitFox 推送的合并请求事件。
 * Connect SSE after page is ready to receive pull request events from GitFox.
 */
$(document).ready(function()
{
    connectSSE();
});

/**
 * 在当前页面用modal加载链接。
 * Load link object page.
 *
 * @param  string $link
 * @access public
 * @return void
 */
window.loadLinkPage = function(link)
{
    $('#linkObject').attr('href', link);
    $('#linkObject').trigger('click');
}

window.loadMergeBtn = function(mergeType)
{
    $.cookie.set('mergeType', mergeType, {expires:config.cookieLife, path:config.webRoot});
    loadCurrentPage('#mr-detail');
}

/**
 * Toggle fullscreen for files tab and recalculate content heights.
 *
 * @access public
 * @return void
 */
window.toggleFilesFullscreen = function()
{
    var $target   = $('#files-tab');
    var wasInFull = $target.hasClass('is-in-fullscreen');

    $target.fullscreen();

    /* Reset height caches so getIframeHeight/getSidebarHeight recalculate. */
    iframeHeight  = 0;
    sidebarHeight = 0;

    setTimeout(function()
    {
        if(!wasInFull)
        {
            /* Entering fullscreen: cache fullscreen-height values. */
            iframeHeight  = $(window).height() - 110;
            sidebarHeight = $(window).height() - 100;
        }

        updateFilesContentHeight();
    }, 300);
};

/**
 * Update iframe and sidebar heights to match current viewport.
 *
 * @access public
 * @return void
 */
window.updateFilesContentHeight = function()
{
    var height     = getIframeHeight();
    var treeHeight = getSidebarHeight();
    var isFS       = $('#files-tab').hasClass('is-in-fullscreen');

    $('#files-tab iframe.repo-iframe').each(function()
    {
        $(this).attr('height', height);
    });

    $('#monacoTree').css('height', (treeHeight - (isFS ? 88 : 8)) + 'px');
};

/* Watch for ZUI fullscreen exit (close button / ESC) and recalculate heights. */
$(function()
{
    var $target = $('#files-tab');
    if(!$target.length) return;

    var observer = new MutationObserver(function(mutations)
    {
        mutations.forEach(function(mutation)
        {
            if(mutation.type === 'attributes' && mutation.attributeName === 'class')
            {
                if(!$target.hasClass('is-in-fullscreen'))
                {
                    /* Fullscreen exited via ZUI close button or ESC. */
                    iframeHeight  = 0;
                    sidebarHeight = 0;
                    setTimeout(function()
                    {
                        updateFilesContentHeight();
                    }, 300);
                }
            }
        });
    });

    observer.observe($target[0], {attributes: true, attributeFilter: ['class']});
});

$(document).off('click', '.batch-btn').on('click', '.batch-btn', function()
{
    const dtable      = zui.DTable.query($(this));
    const checkedList = dtable.$.getChecks();
    if(!checkedList.length) return;

    const form      = new FormData();
    const filter    = [];
    const url       = $(this).data('url');
    const type      = $(this).data('type');
    const isAjaxBtn = $(this).hasClass('ajax-btn');
    let   hasIssue  = false;
    checkedList.forEach((id) => {
        const rowData = dtable.$.getRowInfo(id).data;
        const status  = rowData.status;
        const allowed = (window.batchStatus && window.batchStatus[type] ? window.batchStatus[type] : []).includes(status);
        const blocker = window.batchBlockers && window.batchBlockers[type] ? window.batchBlockers[type] : '';
        if(!allowed || (blocker && rowData[blocker]))
        {
            filter.push('#' + id);
        }
        else
        {
            hasIssue = true;
            form.append('issueIdList[]', id);
        }
    });

    const issueIdList = form.getAll('issueIdList[]');
    if(type !== 'close' && issueIdList.length > 0) $.cookie.set('issueIdList', issueIdList.join('|'));

    if(!hasIssue) return false;

    if(isAjaxBtn)
    {
        /* 与单行按钮一致：先确认再提交，确认成功后清空勾选。 */
        $.ajaxSubmit({url, data: form, confirm: $(this).data('confirm')}).then(([result]) =>
        {
            if(result && result.result === 'success') dtable.$.toggleCheckRows(false);
        });
        return true;
    }

    /* 关闭/忽略保持勾选，提交成功后由页面刷新清空；转 Bug 等离开列表的操作直接清空。 */
    if(['ignore', 'close'].includes(type))
    {
        let modalUrl = url;
        if(type === 'close')
        {
            const separator = modalUrl.includes('?') ? '&' : '?';
            modalUrl += separator + 'issueIdList=' + encodeURIComponent(issueIdList.join(','));
        }
        loadModal(modalUrl);
        return false;
    }

    dtable.$.toggleCheckRows(false);
    loadPage(url, '#mainContainer');
    return true;
});

window.encodeParam = function(value)
{
    return btoa(encodeURIComponent(value)).replace(/\+/g, '_').replace(/\//g, '.');
}

/**
 * 获取文件在代码库查看页面的链接。
 * Resolve the link of the file in the repo view page.
 *
 * @param  object fileDiff
 * @access public
 * @return string
 */
window.resolveFileLink = function(fileDiff)
{
    var file      = encodeParam(fileDiff.name);
    var sourceSHA = encodeParam(ppm.mergeBaseSHA ? ppm.mergeBaseSHA : ppm.targetBranch);

    return $.createLink('repo', 'view', 'repoID=' + ppm.repoID + '&objectID=0&entry=' + file + '&revision=' + sourceSHA);
}

/**
 * 获取文件在代码库查看页面的菜单。
 * Resolve the menu of the file in the repo view page.
 *
 * @param  object fileDiff
 * @access public
 * @return array
 */
window.resolveFileMenu = function(fileDiff)
{
    var file      = encodeParam(fileDiff.name);
    var sourceSHA = encodeParam(ppm.mergeBaseSHA ? ppm.mergeBaseSHA : ppm.targetBranch);
    var targetSHA = encodeParam(ppm.sourceSHA ? ppm.sourceSHA : ppm.sourceBranch);

    return [
        {
            text: ppmLang.downloadDiff,
            icon: 'download',
            url: $.createLink('repo', 'ajaxDownloadDiff', 'repoID=' + ppm.repoID + '&fromRevision=' + sourceSHA + '&toRevision=' + targetSHA + '&filePath=' + file),
        },
    ];
}
