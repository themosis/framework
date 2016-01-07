import $ from 'jquery';
import _ from 'underscore';
import Backbone from 'backbone';
import ItemModel from './ItemModel';
import ItemView from './ItemView';

class ItemsView extends Backbone.View
{
    get events()
    {
        return {
            'click button#themosis-collection-add': 'add',
            'click button#themosis-collection-remove': 'removeSelectedItems'
        };
    }

    initialize()
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
    }

    /**
     * Listen to media frame select event and retrieve the selected files.
     *
     * @return void
     */
    selectedItems()
    {
        let selection = this.frame.state('library').get('selection');

        // Check if a limit is defined. Only filter the selection if selection is larger than the limit.
        if (this.limit)
        {
            let realLimit = ((this.limit - this.collection.length) < 0) ? 0 : this.limit - this.collection.length;
            selection = selection.slice(0, realLimit);
        }

        selection.map(function(attachment)
        {
            this.insertItem(attachment);
        }, this);

    }

    /**
     * Insert selected items to the collection view and its collection.
     *
     * @param attachment The attachment model from the WordPress media API.
     * @return void
     */
    insertItem(attachment)
    {
        // Build a specific model for this attachment.
        let m = new ItemModel({
            'value': attachment.get('id'),
            'src': this.getAttachmentThumbnail(attachment),
            'type': attachment.get('type'),
            'title': attachment.get('title')
        });

        // Build a view for this attachment and pass it its model and current collection.
        let itemView = new ItemView({
            model: m,
            collection: this.collection,
            collectionView: this
        });

        // Add the model to the collection.
        this.collection.add(m);

        // Add the model to the DOM.
        this.$el.find('ul.themosis-collection-list').append(itemView.render().el);
    }

    /**
     * Get the attachment thumbnail URL and returns it.
     *
     * @param {object} attachment The attachment model.
     * @return {string} The attachment thumbnail URL.
     */
    getAttachmentThumbnail(attachment)
    {
        let type = attachment.get('type'),
            url = attachment.get('icon');

        if('image' === type)
        {
            // Check if the _themosis_media size is available.
            let sizes = attachment.get('sizes');

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
    }

    /**
     * Handle the display of the main remove button.
     *
     * @return void
     */
    toggleRemoveButton(items)
    {
        let length = items.length ? true : false;

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
    }

    /**
     * Handle the display of the collection container.
     *
     * @return void
     */
    toggleCollectionContainer(collection)
    {
        let length = collection.length,
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
    }

    /**
     * Triggered when 'add' button is clicked. Open the media library.
     *
     * @param e The event object
     * @return void
     */
    add(e)
    {
        // Check the Add button.
        let addButton = $(e.currentTarget);

        // If button is disabled, return.
        if (addButton.hasClass('disabled')) return;

        this.frame.open();
    }

    /**
     * Triggered when 'remove' button is clicked. Tell view/collection
     * to remove files from the current collection.
     *
     * @return void
     */
    removeSelectedItems()
    {
        // Call parent view to trigger its method to remove files from its collection.
        let selectedItems = this.collection.where({'selected': true});

        this.collection.trigger('removeSelected', selectedItems);
    }

    /**
     * Allow collection items to be sortable using drag&drop.
     *
     * @return void
     */
    sort()
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
}

export default ItemsView;