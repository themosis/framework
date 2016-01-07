import $ from 'jquery';
import _ from 'underscore';
import Backbone from 'backbone';
import ItemModel from './ItemModel';

class ItemsCollection extends Backbone.Collection
{
    get model()
    {
        return ItemModel;
    }

    initialize()
    {
        // Events
        this.on('change:selected', this.onSelect);
        this.on('remove', this.onSelect);
        this.on('add', this.onAdd);
    }

    /**
     * Triggered when a model in the collection changes
     * its 'selected' value.
     *
     * @return void
     */
    onSelect()
    {
        // Check if there are selected items.
        // If one or more items are selected, show the main remove button.
        let selectedItems = this.where({'selected': true});

        this.trigger('itemsSelected', selectedItems);

        // Trigger an event where we can check the length of the collection.
        // Use to hide/show the collection container.
        this.trigger('collectionToggle', this);
    }

    /**
     * Triggered when a model is added in the collection.
     *
     * @return void
     */
    onAdd()
    {
        // Trigger an event in order to check the display of the collection container.
        this.trigger('collectionToggle', this);
    }
}

export default ItemsCollection;