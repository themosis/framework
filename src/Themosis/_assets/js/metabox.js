/**
 * This file supports the custom fields built by the
 * Metabox PHP class.
 *
 * Combination of jQuery & BackboneJS, used in the
 * Wordpress Core.
*/
jQuery(document).ready(function($){

	(function($){

        //------------------------------------------------
        // MEDIA - Custom field
        //------------------------------------------------
		var MediaApp = {
			Models: {},
			Views: {},
			Collections: {}
		};

		// Media View
		MediaApp.Views.Media = Backbone.View.extend({

		    initialize: function(options){

		    },

		    events: {
    		    'click #themosis-media-add': 'add',
    		    'click #themosis-media-clear': 'clear'
		    },

		    /**
    		 * Open the WP media uploader and allow
    		 * the user to add a media file.
		    */
		    add: function(e){
    		    e.preventDefault();

				this.toggleMediaUploader(e.currentTarget);
		    },

		    /**
             * Clear the value of <input /> tag
		    */
		    clear: function(e){
    		    e.preventDefault();

    		    var button = $(e.currentTarget);
    		    button.closest('tr').find('td.themosis-media-input input').val('');
		    },

		    /**
    		 * Toggle the Media Uploader
		    */
		    toggleMediaUploader: function(button){

    		    var button = $(button),
					input = button.closest('tr').find('td.themosis-media-input input');

				window.send_to_editor = function(html)
				{
					// Check if there is an img tag inside html
					var html = $(html);
					var img = html.find('img');

		        	if(img.length !== 0){

		        		var value = img.attr('src');

		        	} else {

		        		var value = html.attr('href');

		        	}

		            // Place image URL to the input field
		            input.val(value);

		            // Close ThickBox
		            if (typeof tb_remove == 'function') {
		            	tb_remove();
		            };
		        };

		        /*
				* This handle the new media manager added in
				* Wordpress 3.5
		        */
		        if(wp !== undefined){

		        	// YOU NEED to pass the button that calls the wp media editor
		        	wp.media.editor.open(button);

		        } else {

		        	/*
					* Old media manager for Wordpress versions < 3.5
					* Will have to be removed a day
		        	*/
		        	tb_show('', 'media-upload.php?post_id=' + this.ID + '&amp;TB_iframe=true');
		        }
		    }

		});

		// Implementation
		var medias = $('tr.themosis-field-media');

		_.each(medias, function(elem, i){

    		// Build a View for each media custom field
    		var mediaField = new MediaApp.Views.Media({
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

	})($);

});