import * as React from "react";

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
                {this.props.children}
            </div>
        );
    }
}

export default MetaboxFooter;