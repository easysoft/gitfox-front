/*
 * @copyright: Copyright 2009-2026 禅道软件（青岛）有限公司(ZenTao Software (Qingdao) Co., Ltd. www.zentao.net)
 * @license: ZPL(http://zpl.pub/page/zplv12.html) or AGPL(https://www.gnu.org/licenses/agpl-3.0.en.html)
 * @author: mengyutong <mengyutong@chandao.com>
 */
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

    /* 关闭/忽略需要填写原因，通过弹窗表单提交；成功后主动清空勾选并刷新列表。 */
    if(['ignore', 'close'].includes(type))
    {
        let modalUrl = url;
        if(type === 'close')
        {
            const separator = modalUrl.includes('?') ? '&' : '?';
            modalUrl += separator + 'issueIdList=' + encodeURIComponent(issueIdList.join(','));
        }

        if(window.manualIssueCloseRefreshHandler) $(document).off('success.zui.ajaxForm', window.manualIssueCloseRefreshHandler);
        window.manualIssueCloseRefreshHandler = function(event)
        {
            const action = String($(event.target).attr('action') || '').toLowerCase();
            if(!action.includes('closemanualissue')) return;

            $(document).off('success.zui.ajaxForm', window.manualIssueCloseRefreshHandler);
            window.manualIssueCloseRefreshHandler = null;
            dtable.$.toggleCheckRows(false);
        };
        $(document).on('success.zui.ajaxForm', window.manualIssueCloseRefreshHandler);
        loadModal(modalUrl);
        return false;
    }

    dtable.$.toggleCheckRows(false);
    loadPage(url, '#mainContainer');
    return true;
});
