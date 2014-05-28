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
            value : '',
            type : 'image',
            size : 'full'
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
                type = selection.get('type');

            // Deal with an application file type.
            // Simply get the URL property.
            if('application' === type){

                this.setField(selection.get('url'));
                this.toggleButtons();

            } else if('image' === type){

                // Check if the defined size is available.
                var sizes = selection.get('sizes'),
                    image = sizes[this.model.get('size')];

                if(undefined !== image){
                    // If available, grab its URL.
                    this.setField(image.url);
                } else {
                    // If not available, take the full size URL.
                    this.setField(selection.get('url'));
                }

                this.toggleButtons();

            }

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
         * Set the media field hidden input value and display html value.
         * Set the model value attribute.
         *
         * @param {string} url
         */
        setField: function(url){

            // Update the model.
            this.model.set({value: url});

            // Update the DOM elements.
            this.input.val(url);
            this.display.html(url);

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
            var cells = this.$el.find('table.themosis-media td');

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
    var mediaFields = $('tr.themosis-field-media');

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

            _.each(this.rows, function(elem){

                // DOM elements.
                var row = $(elem);

                // Backbone elements.
                // Setup row views.
                new InfiniteApp.Views.Row({
                    el: row
                });

            });

            // Setup the views.
            // Set the main ADD button view.
            new InfiniteApp.Views.Add({
                el: this.infinite.find('div.themosis-infinite-add-field-container')
            });

            // Set the main INFINITE view (=rowsCollection View)
            new InfiniteApp.Views.Infinite({
                el: this.infinite.find('table.themosis-infinite>tbody')
            });
        }

    };

    // Single row view
    InfiniteApp.Views.Row = Backbone.View.extend({

        initialize: function(){

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
            console.log('Insert a new row');
        },

        /**
         * Triggered when 'delete' button is clicked.
         */
        remove: function(){
            console.log('Delete the row');
        },

        /**
         * Place the row 'add' button.
         */
        placeButton: function(){
            var plusButton = this.$el.find('.themosis-infinite-add'),
                cellHeight = this.$el.find('td.themosis-infinite-options').height();

            plusButton.css('margin-top', (cellHeight / 2) * -1);
        }

    });

    // Main ADD button view
    InfiniteApp.Views.Add = Backbone.View.extend({

        events: {
            'click button#themosis-infinite-main-add': 'addRow'
        },

        /**
         * Send an event to add a new row.
         */
        addRow: function(){
            vent.trigger('row:add');
        }

    });

    // Infinite view - All rows view
    InfiniteApp.Views.Infinite = Backbone.View.extend({

        initialize: function(){
            // Number of rows.
            this.updateCount();

            console.log(this);

            // Global events.
            vent.on('row:add', this.add, this);
        },

        /**
         * Add a new row to the collection.
         */
        add: function(){
            var row = this.$el.find('tr.themosis-infinite-row').clone(),
                rowView = new InfiniteApp.Views.Row({
                    el: row
                });
            this.$el.append(rowView.el);
            this.updateCount();
            console.log(this);
        },

        /**
         * Update the total number of rows.
         */
        updateCount: function(){
            this.count = this.$el.find('tr.themosis-infinite-row').length;
        }

    });

    // Implementation
    // List all infinite fields.
    var infinites = $('tr.themosis-field-infinite');

    _.each(infinites, function(elem){

        InfiniteApp.init(elem);

    });


})(jQuery);