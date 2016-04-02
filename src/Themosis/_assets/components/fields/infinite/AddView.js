import $ from 'jquery';
import _ from 'underscore';
import Backbone from 'backbone';

class AddView extends Backbone.View
{
    get events()
    {
        return {
            'click button#themosis-infinite-main-add': 'addRow'
        };
    }

    initialize(options)
    {
        this.options = options;
    }

    /**
     * Send an event to add a new row.
     */
    addRow()
    {
        // Calls the infinite parent view method.
        this.options.parent.add();
    }
}

export default AddView;