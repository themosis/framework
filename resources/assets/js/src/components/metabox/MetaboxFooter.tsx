import * as React from 'react';
import Button from '../buttons/Button';

/**
 * Metabox footer.
 */
class MetaboxFooter extends React.Component {
    /**
     * Render the component.
     */
    render() {
        return (
            <div className="themosis__metabox__footer">
                <Button text="Save Changes" primary={true} />
            </div>
        );
    }
}

export default MetaboxFooter;