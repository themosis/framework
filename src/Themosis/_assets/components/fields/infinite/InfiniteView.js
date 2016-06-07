import $ from 'jquery';
import _ from 'underscore';
import Backbone from 'backbone';
import AddView from './AddView';
import RowView from './RowView';

/**
 * Global event object.
 * Used to make component talk to each other.
 *
 * @type {Object}
 */
let vent = _.extend({}, Backbone.Events);

class InfiniteView extends Backbone.View
{
    initialize(options)
    {
        // Retrieve passed parameters.
        this.options = options;

        // Number of rows.
        this.updateCount();

        // Set the limit.
        this.limit();

        // Attach the main "add" button to the view.
        new AddView({
            el: this.$el.closest('.themosis-infinite-container').children('div.themosis-infinite-add-field-container'),
            parent: this
        });

        // Create inner rows view and pass them their parent infinite view.
        this.setRows();

        // Global events.
        vent.on('row:sort', this.update, this);

        // Make it sortable.
        this.sort();
    }

    /**
     * Create inner rows views.
     */
    setRows()
    {
        _.each(this.options.rows, elem =>
        {
            // DOM elements.
            var row = $(elem);

            // Backbone elements.
            // Setup row views.
            new RowView({
                el: row,
                parent: this
            });
        }, this);
    }

    /**
     * Handle the sortable event/feature.
     */
    sort()
    {
        this.$el.sortable({
            helper: function(e, ui)
            {
                ui.children().each(function()
                {
                    $(this).width($(this).width());
                });
                return ui;
            },
            forcePlaceholderSize: true,
            placeholder: 'themosis-ui-state-highlight',
            handle: '.themosis-infinite-order',
            update: function()
            {
                vent.trigger('row:sort');
            }
        });
    }

    /**
     * Grab the first row, reset its values and returns it.
     *
     * @returns {Object} A row view object.
     */
    getFirstRow()
    {
        let row = this.$el.find('tr.themosis-infinite-row').first().clone(),
            rowView = new RowView({
                el: row,
                parent: this
            });

        return rowView.reset();
    }

    /**
     * Add a new row to the collection.
     */
    add()
    {
        // Check the limit.
        if (0 < this.limit && this.count+1 > this.limit) return;

        let row = this.getFirstRow();

        // Add the new row to the DOM.
        this.$el.append(row.el);

        this.update();
    }

    /**
     * Insert a new row before the current one.
     *
     * @param {Object} currentRow The current row view object.
     */
    insert(currentRow)
    {
        // Check the limit.
        if (0 < this.limit && this.count+1 > this.limit) return;

        let row = this.getFirstRow();

        // Add the new row before the current one.
        currentRow.$el.before(row.el);

        this.update();
    }

    /**
     * Remove a row of the collection.
     *
     * @param {Object} row The row view object.
     */
    remove(row)
    {
        // Keep at least one row.
        if (1 >= this.count) return;

        row.$el.remove();

        this.update();
    }

    /**
     * Update the Infinite custom fields values.
     * Update row count.
     * Update row order.
     * Update row inner fields attributes.
     *
     * @return void
     */
    update()
    {
        // Update row count.
        this.updateCount();

        // Rename the fields
        this.rename();
    }

    /**
     * Update the total number of rows.
     */
    updateCount()
    {
        this.count = this.$el.children('tr.themosis-infinite-row').length;
    }

    /**
     * Rename all 'name', 'id' and 'for' attributes.
     */
    rename()
    {
        let rows = this.$el.children('tr.themosis-infinite-row');

        _.each(rows, (row, index) =>
        {
            // Order is 1 based.
            index = String(index + 1);
            row = $(row);

            // Get row fields.
            let fields = row.find('td.themosis-infinite-inner>table>tbody').first().children(), // tr.themosis-field-container element
                order = row.children('td.themosis-infinite-order').children('span');

            // Update the row inner fields.
            _.each(fields, field =>
            {
                // "Field" is the <tr> tag containing all the custom field html.
                field = $(field);

                let input = field.find('input, textarea, select'),
                    label = field.find('th.themosis-label>label'),
                    collectionField = field.find('.themosis-collection-wrapper'); // Check if there is a collection field.
                    // Probably check for an infinite field

                if (!collectionField.length)
                {
                    if (1 < input.length)
                    {
                        // Contains more than one input.
                        _.each(input, io =>
                        {
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
                    let items = collectionField.find('ul.themosis-collection-list input');

                    if (items.length)
                    {
                        // If items input, rename their 'name' attribute.
                        _.each(items, item =>
                        {
                            let itemInput = $(item),
                                name = this.renameName(itemInput.attr('name'), index);
                            itemInput.attr('name', name);
                        }, this);
                    }
                }

            }, this); // End inner fields.

            // Update order display.
            order.html(index);

        }, this);
    }

    /**
     * Rename field input and label.
     *
     * @param {Object} input The field input wrapped in jQuery object.
     * @param {Object} label The field label wrapped in jQuery object.
     * @param {String} index The index used to rename the attributes.
     * @return void
     */
    renameField(input, label, index)
    {
        if ('button' == input.attr('type'))
        {
            if (input.hasClass('wp-picker-clear')) return;
        }

        let fieldId = input.attr('id'),
            fieldName = input.attr('name'),
            id = this.renameId(fieldId, index),
            name = this.renameName(fieldName, index);

        // Update the label 'for' attribute.
        label.attr('for', id);

        // Update input 'id' attribute.
        input.attr('id', id);

        // Update input 'name' attribute.
        input.attr('name', name);
    }

    /**
     * Returns a new ID attribute value.
     *
     * @param {String} currentId
     * @param {String} index
     * @return {String}
     */
    renameId(currentId, index)
    {
        let regex = new RegExp('-([0-9]+)-');
        return currentId.replace(regex, '-' + index + '-');
    }

    /**
     * Returns a new name attribute value.
     *
     * @param {String} currentName
     * @param {String} index
     * @return {String}
     */
    renameName(currentName, index)
    {
        let regex = new RegExp("([0-9]+)\]");
        return currentName.replace(regex, index + ']');
    }

    /**
     * Rename collection field.
     *
     * @param {object} field Collection field wrapped in jQuery
     * @param {int} index The row order/index
     * @return void
     */
    renameCollectionField(field, index)
    {
        let regex = new RegExp("([0-9]+)\]"),
            name = field.data('name'),
            template = field.find('script#themosis-collection-item-template'),
            templateContent = template.html();

        // Update data-name attribute value.
        field.attr('data-name', name.replace(regex, index + ']'));

        // Update backbone template content.
        template.html(templateContent.replace(regex, index + ']'));
    }

    /**
     * Define the limit of rows a user can add.
     */
    limit()
    {
        this.limit = this.$el.data('limit');
    }
}

export default InfiniteView;