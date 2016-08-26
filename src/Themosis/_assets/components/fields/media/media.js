import $ from 'jquery';
import _ from 'underscore';
import Backbone from 'backbone';
import MediaModel from './MediaModel';
import MediaView from './MediaView';
import './media.styl';

/**
 * Implementation.
 */
let mediaFields = $('table.themosis-media').closest('tr, div');

_.each(mediaFields, elem =>
{
    let input = $(elem).find('input.themosis-media-input'),
        types = input.data('type'); // string representation of an array

    let data = new MediaModel({
        value: input.val(),
        type: types.split(','), // return an array from string
        size: input.data('size')
    });

    new MediaView({
        model: data,
        el: $(elem)
    });
});