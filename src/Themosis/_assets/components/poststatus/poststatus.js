import $ from 'jquery';

//------------------------------------------------
// Custom publish metabox.
//------------------------------------------------
// Handle the custom statuses.
let submitdiv = $('#themosisSubmitdiv'),
    editButton = submitdiv.find('.edit-post-status'),
    selectDiv = submitdiv.find('#post-status-select'),
    selectTag = submitdiv.find('#post_status'),
    statusLabel = submitdiv.find('#post-status-display'),
    statusButtons = submitdiv.find('.save-post-status, .cancel-post-status'),
    originalPublish = submitdiv.find('input#original_publish'),
    publishButton = submitdiv.find('input#publish');

// Edit button
editButton.on('click', e =>
{
    e.preventDefault();

    // Show the select option list.
    $(this).hide();
    selectDiv.slideDown(200);
});

// Cancel button or OK buttons
statusButtons.on('click', function(e)
{
    e.preventDefault();

    let button = $(this);

    // If 'ok' button, update label span with status label.
    if (button.hasClass('save-post-status'))
    {
        // Grab selected label.
        let selected = selectTag.find(':selected'),
            label = selected.text(),
            publishText = selected.data('publish');

        // Update label text.
        statusLabel.text(label);

        // Update publish button.
        // Check if 'draft'
        if ('draft' === selected.val())
        {
            // Change value of the "original_publish" input.
            originalPublish.val('auto-draft');
            // Change publish button name attribute.
            publishButton.attr('name', 'save');
        }

        // Change publish button text.
        publishButton.val(publishText);
    }

    // If 'cancel' button, make sure to reset the select tag value.
    if (button.hasClass('cancel-post-status'))
    {
        let selected = selectTag.find('option[selected="selected"]');
        selectTag.val(selected.val());
    }

    // Show back edit button.
    editButton.show();

    // Close select statuses.
    selectDiv.slideUp(200);
});

//------------------------------------------------
// Quick edit select tag.
//------------------------------------------------
import './quickedit';