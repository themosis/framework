/**
 * This file supports the Themosis custom fields.
 * Combination of jQuery & BackboneJS.
 */
(function($){

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


})(jQuery);
