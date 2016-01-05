import Backbone from 'backbone';

class ItemModel extends Backbone.Model
{
    constructor()
    {
        super();
        this.defaults = {
            'selected': false,
            'value': '',  // The media file ID
            'src': '',
            'type': 'image', // The media file URL
            'title': ''
        };
    }
}

export default ItemModel;