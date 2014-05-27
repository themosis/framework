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
            'click #themosis-media-add': 'add',
            'click #themosis-media-delete': 'delete'
        },

        /**
         * Handle event when add button is clicked.
         *
         * @param {object} event
         */
        add: function(event){
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
        delete: function(event){
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
    var InfiniteApp = {
        Models: {},
        Views: {},
        Collections: {},
        vent: _.extend({}, Backbone.Events)
    };

    // Infinite View - Main app view
    InfiniteApp.Views.Infinite = Backbone.View.extend({

        initialize: function(options){
            _.bindAll(this, 'remove', 'insertBefore', 'update');

            options.vent.bind('infinite-remove', this.remove);
            options.vent.bind('infinite-insert', this.insertBefore);
            // Listen to the 'updated' event triggered by the update event of the sortable object
            options.vent.on('infinite-update', this.update);

            this.total = this.updateTotalRows();
            this.setRows();
            this.sort();

        },

        events: {
            // Click event on the main "Add Row" button
            'click #themosis-infinite-main-add': 'addRow'
        },

        /**
         * Unable the fields to be sortable
        */
        sort: function(){
            var container = this.$el.find('#themosis-infinite-sortable>tbody');

            container.sortable({
                helper : function(e, ui) {
                    ui.children().each(function() {
                        $(this).width($(this).width());
                    });
                    return ui;
                },
                forcePlaceholderSize : true,
                placeholder : 'themosis-ui-state-highlight',
                handle : '.themosis-infinite-order',
                update : function(e, ui){
                    InfiniteApp.vent.trigger('infinite-update');
                }
            });
        },

        /*
         * Triggered when sorting the rows. Update the 'name' attributes and count
        */
        update: function(){
            // Update the total number of rows
            this.total = this.updateTotalRows();

            // Rename all 'name' attributes of all fields of each rows
            this.rename();
        },

        /*
         * Set existing rows as a Backbone view
        */
        setRows: function(){
           var rows = this.$el.find('tr.themosis-infinite-row');

           _.each(rows, function(elem, i){

               var row = new InfiniteApp.Views.Row({
                   el: $(elem),
                   vent: this.options.vent
               });

           }, this);
        },

        /*
         * Add a new row to the infinite field
        */
        addRow: function(e){
            e.preventDefault();

            var row = this.$el.find('tr.themosis-infinite-row').first().clone(true).off(),
                newRow = new InfiniteApp.Views.Row({
                    el: row,
                    vent: this.options.vent
                });

            // Insert the new row at the end
            var container = this.$el.find('table.themosis-infinite>tbody');
            container.append(newRow.render().el);

            // Update the total number of rows
            this.total = this.updateTotalRows();

            // Rename all 'name' attributes of all fields of each rows
            this.rename();
        },

        /**
         * Add a new row between 2 existing rows - Called from the row child
         *
         * @param Current row (insert a new one BEFORE this one)
        */
        insertBefore: function(row){
            var ref = this.$el.find('tr.themosis-infinite-row').first().clone(true).off(),
                newRow = new InfiniteApp.Views.Row({
                    el: ref,
                    vent: this.options.vent
                });

            row.before(newRow.render().el);

            // Update the total number of rows
            this.total = this.updateTotalRows();

            // Rename all 'name' attributes of all fields of each rows
            this.rename();
        },

        /*
         * Called when a row is removed. Tell the app to update
         * its row count
        */
        remove: function(){
            this.total = this.updateTotalRows();
            this.rename();
        },

        /*
         * Rename all 'name' attributes of the fields of each row
        */
        rename: function(){
            var rows = this.$el.find('tr.themosis-infinite-row');

            _.each(rows, function(elem, i){
                var row = $(elem);

                // <label> tags
                this.forLabels(row, i+1);
                // <input /> tags
                this.nameInputs(row, i+1);
                // <textarea> tags
                this.nameTextareas(row, i+1);
                // <select> tags
                this.nameSelects(row, i+1);

                // Update the row number
                this.updateRowNumber(row, i+1);

            }, this);
        },

        /*
         * Update the visual number of the row
         *
         * @param Html Element wrap in jQuery
         * @param int - index row number
        */
        updateRowNumber: function(row, num){
           var tag = row.find('.themosis-infinite-order span');

           tag.text(num);
        },

        /*
         * Change the 'for' attribute for <label> tags
         *
         * @param HtmlElement wrap in jQuery
         * @param int - Row number
        */
        forLabels: function(row, num){
            var labels = row.find('th.themosis-label>label');

            _.each(labels, function(elem, i){
                var label = $(elem);
                this.updateLabel(label, num);
            }, this);
        },

        /*
         * Change the 'name' attribute for <input /> tags
         *
         * @param HtmlElement wrap in jQuery
         * @param int - Row number
        */
        nameInputs: function(row, num){
            var inputs = row.find('input');

            _.each(inputs, function(elem, i){
                var input = $(elem);
                this.updateName(input, num);
            }, this);
        },

        /*
         * Change the 'name' attribute for <textarea> tags
         *
         * @param HtmlElement wrap in jQuery
         * @param int - Row number
        */
        nameTextareas: function(row, num){
            var textareas = row.find('textarea');

            _.each(textareas, function(elem, i){
                var textarea = $(elem);
                this.updateName(textarea, num);
            }, this);
        },

        /*
         * Change the 'name' attribute for <select> tags
         *
         * @param HtmlElement wrap in jQuery
         * @param int - Row number
        */
        nameSelects: function(row, num){
            var selects = row.find('select');

            _.each(selects, function(elem, i){
                var select = $(elem);
                this.updateName(select, num);
            }, this);
        },

        /*
         * Update the name attribute of an element
         *
         * @param Html Element wrap in jQuery
         * @param int - Its index
        */
        updateName: function(elem, index){
            var regex = new RegExp("(row[0-9]+)"),
                nameAttr = elem.attr('name');

            nameAttr = nameAttr.replace(regex, "row" + index);

            // Set the new name attribute
            elem.attr('name', nameAttr);

            // Update the ID attr
            var idAttr = elem.attr('id');
            idAttr = idAttr.replace(regex, 'row' + index);

            elem.attr('id', idAttr);
        },

        /*
         * Update the 'for' attribute of a label
         *
         * @param Html Element wrap in jQuery
         * @param int - Its index
        */
        updateLabel: function(elem, index){
            var regex = new RegExp("(row[0-9]+)"),
                forAttr = elem.attr('for');

            forAttr = forAttr.replace(regex, 'row' + index);

            elem.attr('for', forAttr);
        },

        /*
         * Return the total number of rows
         *
         * @return int
        */
        updateTotalRows: function(){
            var rows = this.$el.find('tr.themosis-infinite-row');
                count = rows.length;

            return count;
        }

    });

    // Row View
    // Each row view is copy of the first row with cleared values
    InfiniteApp.Views.Row = Backbone.View.extend({

        initialize: function(options){
            this.vent = InfiniteApp.vent; // Listen and trigger custom events
        },

        /*
         * The render method is only invoked when we add
         * a new row to the app.
        */
        render: function(){
            this.clearValues();
            return this;
        },

        events: {
            'mouseenter .themosis-infinite-options': 'placeButton',
            'click .themosis-infinite-remove': 'remove',
            'click .themosis-infinite-add': 'insert'
        },

        /*
         * Place the '+' button when mouse is over
        */
        placeButton: function(){
            var plusButton = this.$el.find('.themosis-infinite-add'),
                cellHeight = this.$el.find('td.themosis-infinite-options').height();

            plusButton.css('margin-top', (cellHeight / 2) * -1);
        },

        /*
         * Remove the row from the stack
        */
        remove: function(e){
            e.preventDefault();

            var button = $(e.currentTarget),
                row = button.closest('tr.themosis-infinite-row'),
                count = this.$el.closest('tbody').find('tr.themosis-infinite-row');

            // If count is greater than 1, we can delete rows
            // Avoid to delete a row if count is equal to 1.
            if (count.length > 1) {
                row.remove();
                // Tell the InfiniteApp view to update its count of rows
                // This will update the 'name' attributes, ... of all fields for
                // each row.
                this.vent.trigger('infinite-remove');
            }
        },

        /*
         * Add a row to the stack between two existing rows
        */
        insert: function(e){
            e.preventDefault();

            var button = $(e.currentTarget),
                row = button.closest('tr.themosis-infinite-row');
            this.vent.trigger('infinite-insert', row);
        },

        /*
         * Remove all values for each fields
        */
        clearValues: function(){
            this.clearInputs();
            this.clearTextarea();
            this.clearSelects();
        },

        /*
         * Clear <input /> tags
        */
        clearInputs: function(){
            var inputs = this.$el.find('input');

            _.each(inputs, function(elem, i){

                var input = $(elem),
                    type = $(elem).attr('type');

                switch(type){
                    case 'text':
                        input.val('');
                        break;

                    case 'checkbox':
                    case 'radio':
                        input.removeAttr('checked');
                        break;
                    default:
                        input.val('');
                        break;
                }

            });

        },

        /*
         * Clear <textarea> tag
        */
        clearTextarea: function(){
            var textareas = this.$el.find('textarea');

            _.each(textareas, function(elem, i){
                var textarea = $(elem);
                textarea.text('');
            });
        },

        /*
         * Clear <select> tags
        */
        clearSelects: function(){
            var selects = this.$el.find('select');

            _.each(selects, function(elem, i){

                var select = $(elem),
                    options = select.find('option');

                // By default
                select.val('');

                // Clear <option> tags
                _.each(options, function(elem, i){
                    var option = $(elem);
                    option.removeAttr('selected');
                });

            });
        }

    });

    // Implementation
    var infinites = $('tr.themosis-field-infinite');

    _.each(infinites, function(elem, i){

        // Build an infinite view
        var infiniteField = new InfiniteApp.Views.Infinite({
            el: $(elem),
            vent: InfiniteApp.vent
        });

    });

})(jQuery);