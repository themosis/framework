import $ from 'jquery';
import _ from 'underscore';
import Backbone from 'backbone';
import MediaModel from '../media/MediaModel';
import MediaView from '../media/MediaView';
import ItemsCollection from '../collection/ItemsCollection';
import CollectionView from '../collection/ItemsView';

class RowView extends Backbone.View
{
    /*get events()
    {
        return {
            'click .themosis-infinite-options>span.themosis-infinite-add': 'insert',
            'click .themosis-infinite-options>span.themosis-infinite-remove': 'remove'
        };
    }*/

    initialize(options)
    {
        // Retrieve passed parameters
        this.options = options;

        _.bindAll(this, 'placeButton', 'insert', 'remove');
        $(window).on('resize', this.placeButton);

        this.$el.children('.themosis-infinite-options').find('span.themosis-infinite-add').on('click', this.insert);
        this.$el.children('.themosis-infinite-options').find('span.themosis-infinite-remove').on('click', this.remove);
        this.$el.children('.themosis-infinite-options').on('mouseenter', this.placeButton);
    }

    /**
     * Triggered when click on the row 'add' button.
     */
    insert()
    {
        this.options.parent.insert(this);
    }

    /**
     * Triggered when 'delete' button is clicked.
     */
    remove()
    {
        this.options.parent.remove(this);
    }

    /**
     * Place the row 'add' button.
     */
    placeButton()
    {
        let plusButton = this.$el.children('td.themosis-infinite-options').children('.themosis-infinite-add'),
            cellHeight = this.$el.children('td.themosis-infinite-options').height(),
            cellWidth = this.$el.children('td.themosis-infinite-options').width();

        plusButton.css('margin-top', ((cellHeight / 2) - 13) * -1);
        plusButton.css('margin-left', (cellWidth / 2) - 9);
    }

    /**
     * Reset all fields value.
     *
     * @return {Object} The view object.
     */
    reset()
    {
        let fields = this.$el.find('input, textarea, select, div.themosis-collection-wrapper');

        _.each(fields, field =>
        {
            let f = $(field),
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

                case 'button':
                    if (f.hasClass('wp-picker-clear')) return;
                    break;

                default:
                    // Reset <input> tag.
                    this.resetInput(f);
            }

        }, this);

        return this;
    }

    /**
     * Reset <input> value attribute.
     *
     * @param {Object} field The input tag wrapped in jQuery object.
     * @return void
     */
    resetInput(field)
    {
        field.attr('value', '');

        /**
         * Check if color field input.
         * If so, tell the script to create it.
         */
        if (field.hasClass('themosis-color-field'))
        {
            // 0 - Get a reference to parent container.
            let parent = field.closest('td.themosis-field');

            // 1 - Remove the old generated color picker from the DOM.
            parent.find('.wp-picker-container').remove();

            // 2 - Append the input only on DOM (inside parent).
            parent.append(field);

            // 3 - Create the color picker.
            field.wpColorPicker();
        }
    }

    /**
     * Reset <input type="checkbox"> and <input type="radio">.
     *
     * @param {Object} field The input tag wrapped in jQuery object.
     * @return void
     */
    resetCheckable(field)
    {
        field.removeAttr('checked');
    }

    /**
     * Reset <select> tag.
     *
     * @param {Object} field The <select> tag wrapped in Jquery object.
     * @return void
     */
    resetSelect(field)
    {
        let options = field.find('option');

        options.each((i, option) =>
        {
            $(option).removeAttr('selected');
        });
    }

    /**
     * Reset <textarea> tag.
     *
     * @param {Object} field The <textarea> tag wrapped in jQuery object.
     * @return void
     */
    resetTextarea(field)
    {
        field.val('');
    }

    /**
     * Reset the custom media field display.
     *
     * @param {Object} field The media hidden input tag wrapped in jQuery object.
     * @return void
     */
    resetMedia(field)
    {
        let cells = field.closest('td').find('table.themosis-media>tbody>tr').find('.themosis-media-preview, .themosis-media-infos, button'),
            addButton = field.closest('td').find('table.themosis-media>tbody>tr').find('#themosis-media-add'),
            mediaField = field.closest('tr.themosis-field-container');

        // Reset path content
        field.closest('td').find('p.themosis-media__path').html('');

        // Toggle media cells only if it's on "delete" state.
        if (addButton.hasClass('themosis-media--hidden'))
        {
            _.each(cells, elem =>
            {
                elem = $(elem);

                if (elem.hasClass('themosis-media--hidden'))
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
        let data = new MediaModel({
            value: field.val(),
            type: field.data('type'),
            size: field.data('size')
        });

        new MediaView({
            model:data,
            el:mediaField
        });
    }

    /**
     * Reset the collection field.
     *
     * @param {object} f The collection field wrapped in jQuery.
     * @return void
     */
    resetCollection(f)
    {
        let list = f.find('ul.themosis-collection-list'),
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
        var c = new ItemsCollection();

        // Instantiate a collection view.
        new CollectionView({
            collection: c,
            el: f
        });
    }
}

export default RowView;