import Backbone from 'backbone';

class MediaModel extends Backbone.Model
{
    constructor(options)
    {
        super(options);
        this.defaults = {
            value: '', // Register the attachment ID
            type: 'image',
            size: 'full',
            display: '', // The text to display - Actually the attachment ID
            thumbUrl: '', // The src url of the icon/image to use for thumbnail
            title: ''
        };
    }
}

export default MediaModel;