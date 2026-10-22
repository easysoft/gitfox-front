function safe64Encode(value)
{
    return btoa(unescape(encodeURIComponent(value))).replace(/\//g, '.');
}

var checkListRequestID = 0;
var checkListPendingRequestID = 0;

function toggleSubmitButton(disabled)
{
    var $submitButton = $('button[type=submit]');
    $submitButton.toggleClass('disabled', disabled);
    $submitButton.prop('disabled', disabled);
}

function loadCheckList(url, options)
{
    options = $.extend({}, options);

    var requestID    = ++checkListRequestID;
    var beforeUpdate = options.beforeUpdate;
    var complete     = options.complete;

    checkListPendingRequestID = requestID;

    options.beforeUpdate = function(data, opts)
    {
        if(requestID !== checkListRequestID) return false;
        if(beforeUpdate) return beforeUpdate(data, opts);
    };
    options.complete = function(data, error, opts)
    {
        if(requestID === checkListPendingRequestID) checkListPendingRequestID = 0;
        if(requestID !== checkListRequestID)
        {
            if(checkListPendingRequestID) toggleLoading('#createCheckList', true);
            return;
        }
        if(complete) complete(data, error, opts);
    };

    $('#createCheckList').removeClass('hidden');
    toggleLoading('#createCheckList', true);
    return loadTarget(url, '#createCheckList', options);
}

window.loadReviewers = function()
{
    var targetBranch  = $('[name="targetBranch"]').val();
    var sourceBranch  = $('[name="sourceBranch"]').val();
    const canMerge      = $('[data-name=message]').data('canMerge');
    const conflictFiles = $('[data-name=message]').data('conflictFiles');
    var repo = $('[name="repoID"]').val() ? $('[name="repoID"]').val() : repoID;

    checkListRequestID ++;
    toggleSubmitButton(true);

    var targetBranch = safe64Encode(targetBranch);
    var sourceBranch = safe64Encode(sourceBranch);
    if(!sourceBranch || !targetBranch)
    {
        $('#createCheckList').addClass('hidden');
        toggleLoading('#createCheckList', false);
        return;
    }

    if(!canMerge)
    {
        if(conflictFiles.length > 0)
        {
            loadCheckList($.createLink('ppm', 'ajaxGetConflictFiles', 'repoID=' + repo + '&sourceBranch=' + sourceBranch + '&targetBranch=' + targetBranch));
        }
        else
        {
            $('#createCheckList').addClass('hidden');
            toggleLoading('#createCheckList', false);
        }
    }
    else
    {
        loadCheckList($.createLink('ppm', 'ajaxGetCreateCheckList', 'repoID=' + repo + '&sourceBranch=' + sourceBranch + '&targetBranch=' + targetBranch + '&recPerPage=20&pageID=1&tab=commit'), {
            complete: function(data, error)
            {
                if(error) return;
                toggleSubmitButton(false);
            }
        });
    }
}

$(document).on('click', '#createCheckListTabs > .tabs-header > .tabs-nav > .nav-item > a', function(e)
{
    var url = $(this).attr('href');
    if(!url) return;

    e.preventDefault();
    e.stopImmediatePropagation();
    if($(this).hasClass('active')) return;

    loadCheckList(url);
});

$(document).on('change', '[name="sourceBranch"], [name="targetBranch"]', function()
{
    checkListRequestID ++;
    toggleSubmitButton(true);
});

toggleSubmitButton(true);
waitDom('[name=targetBranch]', loadReviewers);
