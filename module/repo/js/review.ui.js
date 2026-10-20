// 人工审查问题列表的批量操作，与 AI 评审问题列表同一套处理流程。
$(document).off('click', '.batch-btn').on('click', '.batch-btn', function()
{
    const dtable = zui.DTable.query($(this));
    const checkedList = dtable.$.getChecks();
    if(!checkedList.length) return;

    const form      = new FormData();
    const url       = $(this).data('url');
    const type      = $(this).data('type');
    const isAjaxBtn = $(this).hasClass('ajax-btn');
    let   hasIssue  = false;

    checkedList.forEach((id) =>
    {
        const rowData = dtable.$.getRowInfo(id).data;
        const allowed = (window.batchStatus && window.batchStatus[type] ? window.batchStatus[type] : []).includes(rowData.status);
        const blocker = window.batchBlockers && window.batchBlockers[type] ? window.batchBlockers[type] : '';
        if(!allowed || (blocker && rowData[blocker])) return;

        hasIssue = true;
        form.append('issueIdList[]', id);
    });

    const issueIdList = form.getAll('issueIdList[]');
    if(issueIdList.length > 0) $.cookie.set('issueIdList', issueIdList.join('|'));

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
