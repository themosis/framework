/**
 * This file supports the Themosis custom fields.
 * Combination of jQuery & BackboneJS.
 */
(function($){

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
            display : '',
            thumbUrl : ''
        }
    });

    // Media view
    MediaApp.Views.MediaView = Backbone.View.extend({

        /**
         * Start the media view.
         * Setup a new media window.
         */
        initialize: function(){

            // Init field properties.
            // The hidden input DOM element.
            this.input = this.$el.find('#themosis-media-input');

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
        addMedia: function(event){
            event.preventDefault();

            // Open the media window
            this.open();
        },

        /**
         * Open the media window and display it.
         *
         * @returns void
         */
        open: function(){
            this.frame.open();
        },

        /**
         * Run when an item is selected in the media library.
         * The event is fired when the "insert" button is clicked.
         *
         * @returns void
         */
        select: function(){

            var selection = this.getItem(),
                type = selection.get('type'),
                val = selection.get('id'),
                display = selection.get('id'),
                thumbUrl = '';

            // Deal with an application file type.
            if('application' === type){

                thumbUrl = thfmk_themosis._themosisAssets + '/images/themosisFileIcon.png';

            // Deal with an image.
            } else if('image' === type){

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
                thumbUrl: thumbUrl
            });

            // Update the DOM elements.
            this.input.val(val);
            this.display.html(display);
            this.thumbnail.attr('src', thumbUrl);

            this.toggleButtons();

        },

        /**
         * Get the selected item from the library.
         *
         * @returns {object} A backbone model object.
         */
        getItem: function(){

            var selection = this.frame.state().get('selection').first();

            return selection;

        },

        /**
         * Handle event when delete button is clicked.
         *
         * @param {object} event
         */
        deleteMedia: function(event){
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
        resetInput: function(){

            this.input.val('');
            this.model.set({value: ''});

        },

        /**
         * Toggle buttons display.
         *
         * @returns void
         */
        toggleButtons: function(){
            var cells = this.$el.find('table.themosis-media .themosis-media-preview, table.themosis-media .themosis-media-infos, table.themosis-media button');

            _.each(cells, function(elem){

                elem = $(elem);

                if(elem.hasClass('themosis-media--hidden')){

                    elem.removeClass('themosis-media--hidden');

                } else {

                    elem.addClass('themosis-media--hidden');

                }

            });
        }

    });

    // Implementation
    var mediaFields = $('table.themosis-media').closest('tr');

    _.each(mediaFields, function(elem){

        var input = $(elem).find('input#themosis-media-input');

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
        init: function(elem){
            this.infinite = $(elem);
            this.rows = this.fetchRows();
            this.setupBackbone();
        },

        /**
         * Grab all the infinite field rows.
         *
         * @return {Array}
         */
        fetchRows: function(){
            return this.infinite.find('tr.themosis-infinite-row');
        },

        /**
         * Initialize the Backbone application.
         *
         * @return void
         */
        setupBackbone: function(){
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

        initialize: function(options){
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
        insert: function(){
            this.options.parent.insert(this);
        },

        /**
         * Triggered when 'delete' button is clicked.
         */
        remove: function(){
            this.options.parent.remove(this);
        },

        /**
         * Place the row 'add' button.
         */
        placeButton: function(){
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
        reset: function(){

            var fields = this.$el.find('input, textarea, select');

            _.each(fields, function(field){

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
        resetInput: function(field){
            field.attr('value', '');
        },

        /**
         * Reset <input type="checkbox"> and <input type="radio">.
         *
         * @param {Object} field The input tag wrapped in jQuery object.
         * @return void
         */
        resetCheckable: function(field){
            field.removeAttr('checked');
        },

        /**
         * Reset <select> tag.
         *
         * @param {Object} field The <select> tag wrapped in Jquery object.
         * @return void
         */
        resetSelect: function(field){
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
        resetTextarea: function(field){
            field.val('');
        },

        /**
         * Reset the custom media field display.
         *
         * @param {Object} field The media hidden input tag wrapped in jQuery object.
         * @return void
         */
        resetMedia: function(field){

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

                    if(elem.hasClass('themosis-media--hidden')){

                        elem.removeClass('themosis-media--hidden');

                    } else {

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
        }

    });

    // Main ADD button view
    InfiniteApp.Views.Add = Backbone.View.extend({

        initialize: function(options){
            this.options = options;
        },

        events: {
            'click button#themosis-infinite-main-add': 'addRow'
        },

        /**
         * Send an event to add a new row.
         */
        addRow: function(){
            // Calls the infinite parent view method.
            this.options.parent.add();
        }

    });

    // Infinite view - All rows view
    InfiniteApp.Views.Infinite = Backbone.View.extend({

        initialize: function(options){

            // Retrieve passed parameters.
            this.options = options;

            // Number of rows.
            this.updateCount();

            // Set the limit.
            this.limit();

            // Attach the main "add" button to the view.
            this.addButton = new InfiniteApp.Views.Add({
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
        setRows: function(){

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
        sort: function(){

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
        getFirstRow: function(){

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
        add: function(){
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
        insert: function(currentRow){
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
        remove: function(row){
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
        update: function(){
            // Update row count.
            this.updateCount();

            // Rename the fields
            this.rename();
        },

        /**
         * Update the total number of rows.
         */
        updateCount: function(){
            this.count = this.$el.find('tr.themosis-infinite-row').length;
        },

        /**
         * Rename all 'name', 'id' and 'for' attributes.
         */
        rename: function(){
            var rows = this.$el.find('tr.themosis-infinite-row');

            _.each(rows, function(row, index){

                // Order is 1 based.
                index = String(index + 1);
                row = $(row);

                // Get row fields.
                var fields = row.find('tr.themosis-field-container'),
                    order = row.children('td.themosis-infinite-order').children('span');

                // Update the row inner fields.
                _.each(fields, function(field){

                    // "Field" is the <tr> tag containing all the custom field html.
                    field = $(field);

                    var input = field.find('input, textarea, select'),
                        label = field.find('th.themosis-label>label');

                    if(1 < input.length){
                        // Contains more than one input.
                        _.each(input, function(io){

                            io = $(io);
                            this.renameField(io, label, index);

                        }, this);

                    } else {
                        // Only one input inside the field.
                        this.renameField(input, label, index);
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
        renameField: function(input, label, index){

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
        renameId: function(currentId, index){

            var regex = new RegExp('([0-9])');

            return currentId.replace(regex, index);
        },

        /**
         * Returns a new name attribute value.
         *
         * @param {String} currentName
         * @param {String} index
         * @return {String}
         */
        renameName: function(currentName, index){

            var regex = new RegExp('([0-9])');

            return currentName.replace(regex, index);
        },

        /**
         * Define the limit of rows a user can add.
         */
        limit: function(){
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