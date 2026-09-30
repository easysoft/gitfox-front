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
        $.ajaxSubmit({url, data: form});
        return true;
    }

    if(['ignore', 'close'].includes(type))
    {
        loadModal(url);
        return false;
    }

    loadPage(url, '#mainContainer');
    return true;
});
