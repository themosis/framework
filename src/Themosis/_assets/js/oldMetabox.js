// Media View
MediaApp.Views.Media = Backbone.View.extend({

    events: {
        'click #themosis-media-add': 'add',
        'click #themosis-media-delete': 'delete'
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
     * Delete the value of the hidden <input /> tag
     */
    delete: function(e){
        e.preventDefault();

        var button = $(e.currentTarget);
        // Remove input value
        button.closest('tr.themosis-field-media').find('td #themosis-media-input').val('');

        // Remove display text
        button.closest('tr').find('td p.themosis-media__path').html('');

        this.toggleButtonDisplay(button);
    },

    /**
     * Show/hide the buttons: Add - Delete
     *
     * @param button
     */
    toggleButtonDisplay: function(button){

        var cells = button.closest('tr').find('td');
        _.each(cells, function(elem){

            elem = $(elem);

            if(elem.hasClass('themosis-media--hidden')){

                elem.removeClass('themosis-media--hidden');

            } else {

                elem.addClass('themosis-media--hidden');

            }

        });

    },

    /**
     * Toggle the Media Uploader
     *
     * @param button
     */
    toggleMediaUploader: function(button){

        button = $(button);
        var	input = button.closest('tr.themosis-field-media').find('td #themosis-media-input'),
            display = button.closest('tr.themosis-field-media').find('td p.themosis-media__path');

        // Remove error display style
        if(display.hasClass('themosis-media__path--error')){
            display.removeClass('themosis-media__path--error');
        }

        window.send_to_editor = function(html)
        {
            // Check if there is an img tag inside html
            html = $(html);
            var img = html.find('img'),
                value = '';

            if(img.length !== 0){

                value = img.attr('src');

            } else {

                value = html.attr('href');

            }

            // Place image URL to the input field
            input.val(value);

            // Place value to the display
            display.html(value);

            // Close ThickBox
            if (typeof tb_remove == 'function') {
                tb_remove();
            }

            // Alert message if no file added...
            if('' == input.val()){
                display.addClass('themosis-media__path--error');
                display.html('No file added. Press delete and try again!');
            }
        };

        // Toggle buttons display
        this.toggleButtonDisplay(button);

        /*
         * This handle the new media manager added in
         * WordPress 3.5
         */
        if(wp !== undefined){

            // YOU NEED to pass the button that calls the wp media editor
            wp.media.editor.open(button);

        } else {

            /*
             * Old media manager for WordPress versions < 3.5
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
    new MediaApp.Views.Media({
        el: $(elem)
    });

});