import $ from 'jquery';
import _ from 'underscore';
import Backbone from 'backbone';
import ItemModel from './ItemModel';
import ItemView from './ItemView';
import ItemsCollection from './ItemsCollection';
import ItemsView from './ItemsView';
import './collection.styl';

// Implementation
let collections = $('div.themosis-collection-wrapper');

_.each(collections, elem =>
{
    // Check if there are files in the rendered collection field.
    // If not, create an empty collection to listen to and attach it to
    // the collection field view. Also create a buttons view and pass it
    // the collection as a dependency.
    let collectionField = $(elem),
        list = collectionField.find('ul.themosis-collection-list'),
        items = list.children();

    // Instantiate a collection.
    let c = new ItemsCollection();

    // Instantiate a collection view.
    let cView = new ItemsView({
        collection: c,
        el: collectionField
    });

    if (items.length)
    {
        _.each(items, el =>
        {
            let item = $(el),
                input = item.find('input');

            let m = new ItemModel({
                'value': parseInt(input.val()),
                'src': item.find('img').attr('src'),
                'type': collectionField.data('type'),
                'title': item.find('.filename').children().text()
            });

            // Add the model to the collection.
            c.add(m);

            // Create an item view instance.
            new ItemView({
                model: m,
                el: item,
                collection: c,
                collectionView: cView
            });
        });
    }
});