import $ from 'jquery';
import _ from 'underscore';
import Backbone from 'backbone';
import MediaModel from './MediaModel';
import MediaView from './MediaView';
import './media.styl';

/**
 * Implementation.
 */
let mediaFields = $('table.themosis-media').closest('tr');

_.each(mediaFields, elem =>
{
    let input = $(elem).find('input.themosis-media-input');

    let data = new MediaModel({
        value: input.val(),
        type: input.data('type'),
        size: input.data('size')
    });

    new MediaView({
        model: data,
        el: $(elem)
    });
});