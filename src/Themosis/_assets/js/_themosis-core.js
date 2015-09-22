/**
 * This file supports the Themosis custom fields.
 * Combination of jQuery & BackboneJS.
 */
(function($){

    //------------------------------------------------
    // COLOR - Custom field
    //------------------------------------------------
    $('.themosis-color-field').wpColorPicker();

    //------------------------------------------------
    // COLLECTION - Custom field
    //------------------------------------------------
    var CollectionApp = {
        Models: {},
        Views: {},
        Collections: {}
    };

    // Model - Individual item
    CollectionApp.Models.Item = Backbone.Model.extend({
        defaults:{
            'selected': false,
            'value': '',  // The media file ID
            'src': '',
            'type': 'image', // The media file URL
            'title': ''
        }
    });

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

    //------------------------------------------------
    // MEDIA - Custom field
    //------------------------------------------------
    var MediaApp = {
        Models: {},
        Views: {},
        Collections: {}
    };

    // Media model
    MediaApp.Models.Media = Backbone.Model.extend({
        defaults: {
            value : '', // Register the attachment ID
            type : 'image',
            size : 'full',
            display : '', // The text to display - Actually the attachment ID
            thumbUrl : '', // The src url of the icon/image to use for thumbnail
            title : ''
        }
    });

    // Media view
    MediaApp.Views.MediaView = Backbone.View.extend({

        /**
         * Start the media view.
         * Setup a new media window.
         */
        initialize: function()
        {
            // Init field properties.
            // The hidden input DOM element.
            this.input = this.$el.find('.themosis-media-input');

            // The <p> DOM element.
            this.display = this.$el.find('p.themosis-media__path');

            // The img thumbnail DOM element.
            this.thumbnail = this.$el.find('img.themosis-media-thumbnail');

            // Init a WordPress media window.
            this.frame = wp.media({
                // Define behaviour of the media window.
                // 'post' if related to a WordPress post.
                // 'select' if use outside WordPress post.
                frame: 'select',
                // Allow or not multiple selection.
                multiple: false,
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
                    type: this.model.get('type')
                }
            });

            // Attach an event on select. Runs when "insert" button is clicked.
            this.frame.on('select', _.bind(this.select, this));
        },

        /**
         * Listen to view events.
         */
        events: {
            'click #themosis-media-add': 'addMedia',
            'click #themosis-media-delete': 'deleteMedia'
        },

        /**
         * Handle event when add button is clicked.
         *
         * @param {object} event
         */
        addMedia: function(event)
        {
            event.preventDefault();

            // Open the media window
            this.open();
        },

        /**
         * Open the media window and display it.
         *
         * @returns void
         */
        open: function()
        {
            this.frame.open();
        },

        /**
         * Run when an item is selected in the media library.
         * The event is fired when the "insert" button is clicked.
         *
         * @returns void
         */
        select: function()
        {
            var selection = this.getItem(),
                type = selection.get('type'),
                val = selection.get('id'),
                display = selection.get('id'),
                thumbUrl = selection.get('icon'), // Default image url to icon.
                title = selection.get('title');

            // If image, get a thumbnail.
            if ('image' === type)
            {
                // Check if the defined size is available.
                var sizes = selection.get('sizes');

                if (undefined !== sizes._themosis_media)
                {
                    thumbUrl = sizes._themosis_media.url;
                }
                else
                {
                    thumbUrl = sizes.full.url;
                }
            }

            // Update the model.
            this.model.set({
                value: val,
                display: display,
                thumbUrl: thumbUrl,
                title: title
            });

            // Update the DOM elements.
            this.input.val(val);
            this.display.html(display);
            this.thumbnail.attr('src', thumbUrl);

            // Update filename
            // and show it if not an image.
            var filename = this.$el.find('div.filename');
            filename.find('div').html(title);

            if ('image' !== type)
            {
                if (!filename.hasClass('show'))
                {
                    filename.addClass('show');
                }
            }

            this.toggleButtons();
        },

        /**
         * Get the selected item from the library.
         *
         * @returns {object} A backbone model object.
         */
        getItem: function()
        {
            var selection = this.frame.state().get('selection').first();

            return selection;
        },

        /**
         * Handle event when delete button is clicked.
         *
         * @param {object} event
         */
        deleteMedia: function(event)
        {
            event.preventDefault();

            // Reset input
            this.resetInput();

            // Toggle buttons
            this.toggleButtons();
        },

        /**
         * Reset the hidden input value and the model.
         *
         * @returns void
         */
        resetInput: function()
        {
            this.input.val('');
            this.model.set({value: ''});
        },

        /**
         * Toggle buttons display.
         *
         * @returns void
         */
        toggleButtons: function()
        {
            var cells = this.$el.find('table.themosis-media .themosis-media-preview, table.themosis-media .themosis-media-infos, table.themosis-media button');

            _.each(cells, function(elem)
            {
                elem = $(elem);

                if(elem.hasClass('themosis-media--hidden'))
                {
                    elem.removeClass('themosis-media--hidden');
                }
                else
                {
                    elem.addClass('themosis-media--hidden');
                }
            });
        }
    });

    // Implementation
    var mediaFields = $('table.themosis-media').closest('tr');

    _.each(mediaFields, function(elem)
    {
        var input = $(elem).find('input.themosis-media-input');

        var data = new MediaApp.Models.Media({
            value: input.val(),
            type: input.data('type'),
            size: input.data('size')
        });

        new MediaApp.Views.MediaView({
            model:data,
            el: $(elem)
        });
    });

    //------------------------------------------------
    // INFINITE - Custom field
    //------------------------------------------------
    /**
     * Global event object.
     * Used to make component talk to each other.
     *
     * @type {Object}
     */
    var vent = _.extend({}, Backbone.Events);

    var InfiniteApp = {
        Models: {},
        Views: {},
        Collections: {},

        // Initialize the infinite fields.
        init: function(elem)
        {
            this.infinite = $(elem);
            this.rows = this.fetchRows();
            this.setupBackbone();
        },

        /**
         * Grab all the infinite field rows.
         *
         * @return {Array}
         */
        fetchRows: function()
        {
            return this.infinite.find('tr.themosis-infinite-row');
        },

        /**
         * Initialize the Backbone application.
         *
         * @return void
         */
        setupBackbone: function()
        {
            // Setup the views.
            // Set the main INFINITE view (=rowsCollection View)
            new InfiniteApp.Views.Infinite({
                el: this.infinite.find('table.themosis-infinite>tbody'),
                rows: this.rows
            });
        }

    };

    // Single row view
    InfiniteApp.Views.Row = Backbone.View.extend({

        initialize: function(options)
        {
            // Retrieve passed parameters
            this.options = options;

            _.bindAll(this, 'placeButton');
            $(window).on('resize', this.placeButton);
        },

        events: {
            'mouseenter .themosis-infinite-options': 'placeButton',
            'click span.themosis-infinite-add': 'insert',
            'click span.themosis-infinite-remove': 'remove'
        },

        /**
         * Triggered when click on the row 'add' button.
         */
        insert: function()
        {
            this.options.parent.insert(this);
        },

        /**
         * Triggered when 'delete' button is clicked.
         */
        remove: function()
        {
            this.options.parent.remove(this);
        },

        /**
         * Place the row 'add' button.
         */
        placeButton: function()
        {
            var plusButton = this.$el.find('.themosis-infinite-add'),
                cellHeight = this.$el.find('td.themosis-infinite-options').height(),
                cellWidth = this.$el.find('td.themosis-infinite-options').width();

            plusButton.css('margin-top', ((cellHeight / 2) - 13) * -1);
            plusButton.css('margin-left', (cellWidth / 2) - 9);
        },

        /**
         * Reset all fields value.
         *
         * @return {Object} The view object.
         */
        reset: function()
        {
            var fields = this.$el.find('input, textarea, select, div.themosis-collection-wrapper');

            _.each(fields, function(field)
            {
                var f = $(field),
                    type = f.data('field');

                switch(type){

                    case 'textarea':
                        // Reset <textarea> input
                        this.resetTextarea(f);
                        break;

                    case 'checkbox':
                    case 'radio':
                        // Reset <input type="checkbox|radio">
                        this.resetCheckable(f);
                        break;

                    case 'select':
                        // Reset <select> tag.
                        this.resetSelect(f);
                        break;

                    case 'media':
                        // Reset <input type="hidden">
                        this.resetInput(f);
                        // Reset media value display and set a new backbone object media.
                        this.resetMedia(f);
                        break;

                    case 'collection':
                        // Reset collection field backbone objects.
                        this.resetCollection(f);

                        break;

                    default:
                        // Reset <input> tag.
                        this.resetInput(f);
                }

            }, this);

            return this;

        },

        /**
         * Reset <input> value attribute.
         *
         * @param {Object} field The input tag wrapped in jQuery object.
         * @return void
         */
        resetInput: function(field)
        {
            field.attr('value', '');
        },

        /**
         * Reset <input type="checkbox"> and <input type="radio">.
         *
         * @param {Object} field The input tag wrapped in jQuery object.
         * @return void
         */
        resetCheckable: function(field)
        {
            field.removeAttr('checked');
        },

        /**
         * Reset <select> tag.
         *
         * @param {Object} field The <select> tag wrapped in Jquery object.
         * @return void
         */
        resetSelect: function(field)
        {
            var options = field.find('option');

            options.each(function(i, option){

                $(option).removeAttr('selected');

            });
        },

        /**
         * Reset <textarea> tag.
         *
         * @param {Object} field The <textarea> tag wrapped in jQuery object.
         * @return void
         */
        resetTextarea: function(field)
        {
            field.val('');
        },

        /**
         * Reset the custom media field display.
         *
         * @param {Object} field The media hidden input tag wrapped in jQuery object.
         * @return void
         */
        resetMedia: function(field)
        {
            var cells = field.closest('td').find('table.themosis-media>tbody>tr').find('.themosis-media-preview, .themosis-media-infos, button'),
                addButton = field.closest('td').find('table.themosis-media>tbody>tr').find('#themosis-media-add'),
                mediaField = field.closest('tr.themosis-field-container');

            // Reset path content
            field.closest('td').find('p.themosis-media__path').html('');

            // Toggle media cells only if it's on "delete" state.
            if (addButton.hasClass('themosis-media--hidden'))
            {
                _.each(cells, function(elem){

                    elem = $(elem);

                    if(elem.hasClass('themosis-media--hidden'))
                    {
                        elem.removeClass('themosis-media--hidden');
                    }
                    else
                    {
                        elem.addClass('themosis-media--hidden');
                    }

                });
            }

            // Set a new backbone object for the media field.
            var data = new MediaApp.Models.Media({
                value: field.val(),
                type: field.data('type'),
                size: field.data('size')
            });

            new MediaApp.Views.MediaView({
                model:data,
                el:mediaField
            });
        },

        /**
         * Reset the collection field.
         *
         * @param {object} f The collection field wrapped in jQuery.
         * @return void
         */
        resetCollection: function(f)
        {
            var list = f.find('ul.themosis-collection-list'),
                container = f.find('div.themosis-collection-container');

            // Delete all items <li>
            list.children('li').remove();

            // Hide the collection container
            if (container.hasClass('show'))
            {
                container.removeClass('show');
            }

            // Create new collection field instance - Implementation.
            // Instantiate a collection.
            var c = new CollectionApp.Collections.Collection();

            // Instantiate a collection view.
            new CollectionApp.Views.Collection({
                collection: c,
                el: f
            });
        }

    });

    // Main ADD button view
    InfiniteApp.Views.Add = Backbone.View.extend({

        initialize: function(options)
        {
            this.options = options;
        },

        events: {
            'click button#themosis-infinite-main-add': 'addRow'
        },

        /**
         * Send an event to add a new row.
         */
        addRow: function()
        {
            // Calls the infinite parent view method.
            this.options.parent.add();
        }

    });

    // Infinite view - All rows view
    InfiniteApp.Views.Infinite = Backbone.View.extend({

        initialize: function(options)
        {
            // Retrieve passed parameters.
            this.options = options;

            // Number of rows.
            this.updateCount();

            // Set the limit.
            this.limit();

            // Attach the main "add" button to the view.
            new InfiniteApp.Views.Add({
                el: this.$el.closest('.themosis-infinite-container').find('div.themosis-infinite-add-field-container'),
                parent: this
            });

            // Create inner rows view and pass them their parent infinite view.
            this.setRows();

            // Global events.
            vent.on('row:sort', this.update, this);

            // Make it sortable.
            this.sort();
        },

        /**
         * Create inner rows views.
         */
        setRows: function()
        {
            _.each(this.options.rows, function(elem){

                // DOM elements.
                var row = $(elem);

                // Backbone elements.
                // Setup row views.
                new InfiniteApp.Views.Row({
                    el: row,
                    parent: this
                });

            }, this);
        },

        /**
         * Handle the sortable event/feature.
         */
        sort: function()
        {
            this.$el.sortable({
                helper : function(e, ui) {
                    ui.children().each(function() {
                        $(this).width($(this).width());
                    });
                    return ui;
                },
                forcePlaceholderSize : true,
                placeholder : 'themosis-ui-state-highlight',
                handle : '.themosis-infinite-order',
                update : function(){
                    vent.trigger('row:sort');
                }
            });
        },

        /**
         * Grab the first row, reset its values and returns it.
         *
         * @returns {Object} A row view object.
         */
        getFirstRow: function()
        {
            var row = this.$el.find('tr.themosis-infinite-row').first().clone(),
                rowView = new InfiniteApp.Views.Row({
                    el: row,
                    parent: this
                });

            return rowView.reset();
        },

        /**
         * Add a new row to the collection.
         */
        add: function()
        {
            // Check the limit.
            if(0 < this.limit && this.count+1 > this.limit) return;

            var row = this.getFirstRow();

            // Add the new row to the DOM.
            this.$el.append(row.el);

            this.update();
        },

        /**
         * Insert a new row before the current one.
         *
         * @param {Object} currentRow The current row view object.
         */
        insert: function(currentRow)
        {
            // Check the limit.
            if(0 < this.limit && this.count+1 > this.limit) return;

            var row = this.getFirstRow();

            // Add the new row before the current one.
            currentRow.$el.before(row.el);

            this.update();
        },

        /**
         * Remove a row of the collection.
         *
         * @param {Object} row The row view object.
         */
        remove: function(row)
        {
            // Keep at least one row.
            if(1 >= this.count) return;

            row.$el.remove();

            this.update();
        },

        /**
         * Update the Infinite custom fields values.
         * Update row count.
         * Update row order.
         * Update row inner fields attributes.
         *
         * @return void
         */
        update: function()
        {
            // Update row count.
            this.updateCount();

            // Rename the fields
            this.rename();
        },

        /**
         * Update the total number of rows.
         */
        updateCount: function()
        {
            this.count = this.$el.find('tr.themosis-infinite-row').length;
        },

        /**
         * Rename all 'name', 'id' and 'for' attributes.
         */
        rename: function()
        {
            var rows = this.$el.find('tr.themosis-infinite-row');

            _.each(rows, function(row, index)
            {
                // Order is 1 based.
                index = String(index + 1);
                row = $(row);

                // Get row fields.
                var fields = row.find('tr.themosis-field-container'),
                    order = row.children('td.themosis-infinite-order').children('span');

                // Update the row inner fields.
                _.each(fields, function(field)
                {
                    // "Field" is the <tr> tag containing all the custom field html.
                    field = $(field);

                    var input = field.find('input, textarea, select'),
                        label = field.find('th.themosis-label>label'),
                        collectionField = field.find('.themosis-collection-wrapper'); // Check if there is a collection field.

                    if (!collectionField.length)
                    {
                        if (1 < input.length)
                        {
                            // Contains more than one input.
                            _.each(input, function(io){

                                io = $(io);
                                this.renameField(io, label, index);

                            }, this);

                        }
                        else
                        {
                            // Only one input inside the field.
                            this.renameField(input, label, index);
                        }
                    }
                    else
                    {
                        // Collection field - Set its index/order as data-order.
                        // If there is collectionField - Update its order/index property.
                        collectionField.attr('data-order', index);
                        this.renameCollectionField(collectionField, index);

                        // Check if there are items
                        var items = collectionField.find('ul.themosis-collection-list input');

                        if (items.length)
                        {
                            // If items input, rename their 'name' attribute.
                            _.each(items, function(item)
                            {
                                var itemInput = $(item),
                                    name = this.renameName(itemInput.attr('name'), index);
                                itemInput.attr('name', name);
                            }, this);
                        }
                    }

                }, this); // End inner fields.

                // Update order display.
                order.html(index);

            }, this);
        },

        /**
         * Rename field input and label.
         *
         * @param {Object} input The field input wrapped in jQuery object.
         * @param {Object} label The field label wrapped in jQuery object.
         * @param {String} index The index used to rename the attributes.
         * @return void
         */
        renameField: function(input, label, index)
        {
            var fieldId = input.attr('id'),
                fieldName = input.attr('name'),
                id = this.renameId(fieldId, index),
                name = this.renameName(fieldName, index);

            // Update the label 'for' attribute.
            label.attr('for', id);

            // Update input 'id' attribute.
            input.attr('id', id);

            // Update input 'name' attribute.
            input.attr('name', name);

        },

        /**
         * Returns a new ID attribute value.
         *
         * @param {String} currentId
         * @param {String} index
         * @return {String}
         */
        renameId: function(currentId, index)
        {
            var regex = new RegExp('-([0-9]+)-');

            return currentId.replace(regex, '-' + index + '-');
        },

        /**
         * Returns a new name attribute value.
         *
         * @param {String} currentName
         * @param {String} index
         * @return {String}
         */
        renameName: function(currentName, index)
        {
            var regex = new RegExp("([0-9]+)\]");

            return currentName.replace(regex, index + ']');
        },

        /**
         * Rename collection field.
         *
         * @param {object} field Collection field wrapped in jQuery
         * @param {int} index The row order/index
         * @return void
         */
        renameCollectionField: function(field, index)
        {
            var regex = new RegExp("([0-9]+)\]"),
                name = field.data('name'),
                template = field.find('script#themosis-collection-item-template'),
                templateContent = template.html();

            // Update data-name attribute value.
            field.attr('data-name', name.replace(regex, index + ']'));

            // Update backbone template content.
            template.html(templateContent.replace(regex, index + ']'));
        },

        /**
         * Define the limit of rows a user can add.
         */
        limit: function()
        {
            this.limit = this.$el.data('limit');
        }

    });

    // Implementation
    // List all infinite fields.
    var infinites = $('div.themosis-infinite-container').closest('tr');

    _.each(infinites, function(elem){

        InfiniteApp.init(elem);

    });

    //------------------------------------------------
    // Custom publish metabox.
    //------------------------------------------------
    // Handle the custom statuses.
    var submitdiv = $('#themosisSubmitdiv'),
        editButton = submitdiv.find('.edit-post-status'),
        selectDiv = submitdiv.find('#post-status-select'),
        selectTag = submitdiv.find('#post_status'),
        statusLabel = submitdiv.find('#post-status-display'),
        statusButtons = submitdiv.find('.save-post-status, .cancel-post-status'),
        originalPublish = submitdiv.find('input#original_publish'),
        publishButton = submitdiv.find('input#publish');

    // Edit button
    editButton.on('click', function(e)
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

        var button = $(this);

        // If 'ok' button, update label span with status label.
        if (button.hasClass('save-post-status'))
        {
            // Grab selected label.
            var selected = selectTag.find(':selected'),
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
            var selected = selectTag.find('option[selected="selected"]');
            selectTag.val(selected.val());
        }

        // Show back edit button.
        editButton.show();

        // Close select statuses.
        selectDiv.slideUp(200);
    });


})(jQuery);
