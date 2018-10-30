import $ from 'jquery';
import _ from 'lodash';

(function ($, _) {
    if (! themosisGlobal.post_types) {
        return;// Check if global object is defined first.
    }

    let cpts = themosisGlobal.post_types,
        cptInput = $('input.post_type_page'),
        cpt = cptInput.val(),
        select = $('.inline-edit-status select[name=_status]');

    // Check if current post type screen use custom statuses.
    let keys = _.keys(cpts); // Grab object keys first level down.

    if (-1 === keys.indexOf(cpt)) {
        return; // Return false if cpt is not found in the keys array. If so, stop and return.
    }

    // Clean select tag
    // Keep Draft option only.
    select.find('option').remove();

    // Loop through the statuses
    _.each(cpts[cpt]['statuses'], (obj, key) => {
        select.append(`<option value="${key}">${obj.label}</option>`);
    });
})($, _);