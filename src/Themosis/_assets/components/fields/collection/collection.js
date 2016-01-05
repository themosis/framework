import $ from 'jquery';
import Backbone from 'backbone';
import _ from 'underscore';
import ItemModel from './ItemModel';

var CollectionApp = {
    Models: {},
    Views: {},
    Collections: {}
};

console.log(new ItemModel());

// View - Individual item
CollectionApp.Views.Item = Backbone.View.extend({

    tagName: 'li',

    template: '#themosis-collection-item-template',

    initialize: function(options)
    {
        this.collectionView = options.collectionView;
        this.listenTo(this.collection, 'removeSelected', this.removeSelection);
    },

    /**
     * Render the new added items.
     *
     * @returns {CollectionApp.Views.Item}
     */
    render: function()
    {
        var template = _.template(this.collectionView.$el.find(this.template).html());
        this.$el.html(template(this.model.toJSON()));

        // Check model type property. If not an image, add the 'icon' class to the img tag.
        if ('image' !== this.model.get('type'))
        {
            this.$el.find('img').addClass('icon');
            this.$el.find('.filename').addClass('show');
        }

        return this;
    },

    events: {
        'click div.themosis-collection__item': 'select',
        'click a.check' : 'removeItem'
    },

    /**
     * Triggered when the item image is clicked. Set the state of
     * the element as selected so the collection can perform action into it.
     *
     * @return void
     */
    select: function()
    {
        // Change the state of the item as selected
        var item = this.$el.children('div.themosis-collection__item');

        if (item.hasClass('selected'))
        {
            // Deselected
            item.removeClass('selected');
            this.model.set('selected', false);
        }
        else
        {
            // Selected
            item.addClass('selected');
            this.model.set('selected', true);
        }
    },

    /**
     * Remove the selected items/models from the collection.
     * When an item is removed individually, an event is sent to
     * the collection which will remove the model from its list.
     *
     * @param items The selected items to be removed.
     * @return void
     */
    removeSelection: function(items)
    {
        _.each(items, function(elem)
        {
            // If this view model is equal to the passed model
            // remove it.
            if (this.model.cid === elem.cid)
            {
                this.remove();
                this.collection.remove(this.model);
            }
        }, this);
    },

    /**
     * Triggered when the '-' button is clicked. Remove the item
     * from the current collection.
     *
     * @param {object} e The event object.
     * @return void
     */
    removeItem: function(e)
    {
        e.preventDefault();

        // Remove the view item
        this.remove();

        // Remove from the collection
        this.collection.remove(this.model);
    }

});

// Collection - Collection of items
CollectionApp.Collections.Collection = Backbone.Collection.extend({

    model: CollectionApp.Models.Item,

    initialize: function()
    {
        // Listen to events
        this.on('change:selected', this.onSelect);
        this.on('remove', this.onSelect);
        this.on('add', this.onAdd);
    },

    /**
     * Triggered when a model in the collection changes
     * its 'selected' value.
     *
     * @return void
     */
    onSelect: function()
    {
        // Check if there are selected items.
        // If one or more items are selected, show the main remove button.
        var selectedItems = this.where({'selected': true});

        this.trigger('itemsSelected', selectedItems);

        // Trigger an event where we can check the length of the collection.
        // Use to hide/show the collection container.
        this.trigger('collectionToggle', this);
    },

    /**
     * Triggered when a model is added in the collection.
     *
     * @return void
     */
    onAdd: function()
    {
        // Trigger an event in order to check the display of the collection container.
        this.trigger('collectionToggle', this);
    }

});

// View - Collection
CollectionApp.Views.Collection = Backbone.View.extend({

    initialize: function()
    {
        // Bind to collection events
        this.collection.bind('itemsSelected', this.toggleRemoveButton, this);
        this.collection.bind('collectionToggle', this.toggleCollectionContainer, this);

        // Init a WordPress media window.
        this.frame = wp.media({
            // Define behaviour of the media window.
            // 'post' if related to a WordPress post.
            // 'select' if use outside WordPress post.
            frame: 'select',
            // Allow or not multiple selection.
            multiple: true,
            // The displayed title.
            title: 'Insert media',
            // The button behaviour
            button: {
                text: 'Insert',
                close: true
            },
            // Type of files shown in the library.
            // 'image', 'application' (pdf, doc,...)
            library:{
                type: this.$el.data('type')
            }
        });

        // Attach an event on select. Runs when "insert" button is clicked.
        this.frame.on('select', _.bind(this.selectedItems, this));

        // Grab the limit.
        this.limit = parseInt(this.$el.data('limit'));

        // Init the sortable feature.
        this.sort();
    },

    /**
     * Listen to media frame select event and retrieve the selected files.
     *
     * @return void
     */
    selectedItems: function()
    {
        var selection = this.frame.state('library').get('selection');

        // Check if a limit is defined. Only filter the selection if selection is larger than the limit.
        if (this.limit)
        {
            var realLimit = ((this.limit - this.collection.length) < 0) ? 0 : this.limit - this.collection.length;
            selection = selection.slice(0, realLimit);
        }

        selection.map(function(attachment)
        {
            this.insertItem(attachment);
        }, this);

    },

    /**
     * Insert selected items to the collection view and its collection.
     *
     * @param attachment The attachment model from the WordPress media API.
     * @return void
     */
    insertItem: function(attachment)
    {
        // Build a specific model for this attachment.
        var m = new CollectionApp.Models.Item({
            'value': attachment.get('id'),
            'src': this.getAttachmentThumbnail(attachment),
            'type': attachment.get('type'),
            'title': attachment.get('title')
        });

        // Build a view for this attachment and pass it its model and current collection.
        var itemView = new CollectionApp.Views.Item({
            model: m,
            collection: this.collection,
            collectionView: this
        });

        // Add the model to the collection.
        this.collection.add(m);

        // Add the model to the DOM.
        this.$el.find('ul.themosis-collection-list').append(itemView.render().el);
    },

    /**
     * Get the attachment thumbnail URL and returns it.
     *
     * @param {object} attachment The attachment model.
     * @return {string} The attachment thumbnail URL.
     */
    getAttachmentThumbnail: function(attachment)
    {
        var type = attachment.get('type'),
            url = attachment.get('icon');

        if('image' === type)
        {
            // Check if the _themosis_media size is available.
            var sizes = attachment.get('sizes');

            if (undefined !== sizes._themosis_media)
            {
                url = sizes._themosis_media.url;
            }
            else
            {
                // Original image is less than 100px.
                url = sizes.full.url;
            }
        }

        return url;
    },

    /**
     * Handle the display of the main remove button.
     *
     * @return void
     */
    toggleRemoveButton: function(items)
    {
        var length = items.length ? true : false;

        if (length)
        {
            // Show the main remove button.
            this.$el.find('button#themosis-collection-remove').addClass('show');
        }
        else
        {
            // Hide the main remove button.
            this.$el.find('button#themosis-collection-remove').removeClass('show');
        }
    },

    /**
     * Handle the display of the collection container.
     *
     * @return void
     */
    toggleCollectionContainer: function(collection)
    {
        var length = collection.length,
            addButton = this.$el.find('button#themosis-collection-add'),
            container = this.$el.find('div.themosis-collection-container');

        // Check the number of collection items.
        // If total is larger or equal to length, disable the add button.
        if (this.limit && this.limit <= length)
        {
            addButton.addClass('disabled');
        }
        else
        {
            // Re-activate the ADD button if there are less items than the limit.
            addButton.removeClass('disabled');
        }

        // Show the collection container if there are items in collection.
        if (length)
        {
            container.addClass('show');
        }
        else
        {
            // Hide the collection container.
            container.removeClass('show');
        }
    },

    events:{
        'click button#themosis-collection-add': 'add',
        'click button#themosis-collection-remove': 'removeSelectedItems'
    },

    /**
     * Triggered when 'add' button is clicked. Open the media library.
     *
     * @param e The event object
     * @return void
     */
    add: function(e)
    {
        // Check the Add button.
        var addButton = $(e.currentTarget);

        // If button is disabled, return.
        if (addButton.hasClass('disabled')) return;

        this.frame.open();
    },

    /**
     * Triggered when 'remove' button is clicked. Tell view/collection
     * to remove files from the current collection.
     *
     * @return void
     */
    removeSelectedItems: function()
    {
        // Call parent view to trigger its method to remove files from its collection.
        var selectedItems = this.collection.where({'selected': true});

        this.collection.trigger('removeSelected', selectedItems);
    },

    /**
     * Allow collection items to be sortable using drag&drop.
     *
     * @return void
     */
    sort: function()
    {
        this.$el.find('ul.themosis-collection-list').sortable({
            helper : function(e, ui) {
                ui.children().each(function() {
                    $(this).width($(this).width());
                });
                return ui;
            },
            forcePlaceholderSize : true,
            placeholder : 'themosis-collection-ui-state-highlight',
            handle : '.themosis-collection__item'
        });
    }

});

// Implementation
var collections = $('div.themosis-collection-wrapper');

_.each(collections, function(elem)
{
    // Check if there are files in the rendered collection field.
    // If not, create an empty collection to listen to and attach it to
    // the collection field view. Also create a buttons view and pass it
    // the collection as a dependency.
    var collectionField = $(elem),
        list = collectionField.find('ul.themosis-collection-list'),
        items = list.children();

    // Instantiate a collection.
    var c = new CollectionApp.Collections.Collection();

    // Instantiate a collection view.
    var cView = new CollectionApp.Views.Collection({
        collection: c,
        el: collectionField
    });

    if (items.length)
    {
        _.each(items, function(el)
        {
            var item = $(el),
                input = item.find('input');

            var m = new CollectionApp.Models.Item({
                'value': parseInt(input.val()),
                'src': item.find('img').attr('src'),
                'type': collectionField.data('type'),
                'title': item.find('.filename').children().text()
            });

            // Add the model to the collection.
            c.add(m);

            // Create an item view instance.
            new CollectionApp.Views.Item({
                model: m,
                el: item,
                collection: c,
                collectionView: cView
            });
        });
    }
});