import $ from 'jquery';
import _ from 'underscore';
import Backbone from 'backbone';

class MediaView extends Backbone.View
{
    /**
     * View events.
     *
     * @returns {{click #themosis-media-add: string, click #themosis-media-delete: string}}
     */
    get events()
    {
        return {
            'click #themosis-media-add': 'addMedia',
            'click #themosis-media-delete': 'deleteMedia'
        };
    }

    initialize()
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
    }

    /**
     * Handle event when add button is clicked.
     *
     * @param {object} event
     * @return void
     */
    addMedia(event)
    {
        event.preventDefault();

        // Open the media window
        this.open();
    }

    /**
     * Open the media library window and display it.
     *
     * @return void
     */
    open()
    {
        this.frame.open();
    }

    /**
     * Run when an item is selected in the media library.
     * The event is fired when the "insert" button is clicked.
     *
     * @return void
     */
    select()
    {
        let selection = this.getItem(),
            type = selection.get('type'),
            val = selection.get('id'),
            display = selection.get('id'),
            thumbUrl = selection.get('icon'), // Default image url to icon.
            title = selection.get('title');

        // If image, get a thumbnail.
        if ('image' === type)
        {
            // Check if the defined size is available.
            let sizes = selection.get('sizes');

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
        let filename = this.$el.find('div.filename');
        filename.find('div').html(title);

        if ('image' !== type)
        {
            if (!filename.hasClass('show'))
            {
                filename.addClass('show');
            }
        }

        this.toggleButtons();
    }

    /**
     * Get the selected item from the library.
     *
     * @returns {object} A backbone model object.
     */
    getItem()
    {
        return this.frame.state().get('selection').first();
    }

    /**
     * Handle event when delete button is clicked.
     *
     * @param {object} event
     */
    deleteMedia(event)
    {
        event.preventDefault();

        // Reset input
        this.resetInput();

        // Toggle buttons
        this.toggleButtons();
    }

    /**
     * Reset the hidden input value and the model.
     *
     * @returns void
     */
    resetInput()
    {
        this.input.val('');
        this.model.set({value: ''});
    }

    /**
     * Toggle buttons display.
     *
     * @returns void
     */
    toggleButtons()
    {
        let cells = this.$el.find('table.themosis-media .themosis-media-preview, table.themosis-media .themosis-media-infos, table.themosis-media button');

        _.each(cells, elem =>
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
}

export default MediaView;