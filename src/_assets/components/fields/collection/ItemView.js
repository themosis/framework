import $ from 'jquery';
import _ from 'underscore';
import Backbone from 'backbone';

class ItemView extends Backbone.View
{
    get tagName()
    {
        return 'li';
    }

    get template()
    {
        return '#themosis-collection-item-template';
    }

    get events()
    {
        return {
            'click div.themosis-collection__item': 'select',
            'click a.check': 'removeItem'
        };
    }

    initialize(options)
    {
        this.collectionView = options.collectionView;
        this.listenTo(this.collection, 'removeSelected', this.removeSelection);
    }

    /**
     * Render the collection item.
     *
     * @returns {ItemView}
     */
    render()
    {
        let template = _.template(this.collectionView.$el.find(this.template).html());
        this.$el.html(template(this.model.toJSON()));

        if ('image' !== this.model.get('type'))
        {
            this.$el.find('img').addClass('icon');
            this.$el.find('.filename').addClass('show');
        }

        return this;
    }

    /**
     * Triggered when the item image is clicked. Set the state of
     * the element as selected so the collection can perform action into it.
     *
     * @return void
     */
    select()
    {
        let item = this.$el.children('div.themosis-collection__item');

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
    }

    /**
     * Remove the selected items/models from the collection.
     * When an item is removed individually, an event is sent to
     * the collection which will remove the model from its list.
     *
     * @param items The selected items to be removed.
     * @return void
     */
    removeSelection(items)
    {
        _.each(items, elem =>
        {
            // If this view model is equal to the passed model
            // remove it.
            if (this.model.cid === elem.cid)
            {
                this.remove();
                this.collection.remove(this.model);
            }
        }, this);
    }

    /**
     * Triggered when the '-' button is clicked. Remove the item
     * from the current collection.
     *
     * @param {object} e The event object.
     * @return void
     */
    removeItem(e)
    {
        e.preventDefault();

        // Remove the view item
        this.remove();

        // Remove from the collection
        this.collection.remove(this.model);
    }
}

export default ItemView;