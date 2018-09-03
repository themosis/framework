import React from 'react';

/**
 * Metabox container component.
 */
class Metabox extends React.Component {
    /**
     * Render component UI.
     */
    render() {
        return (
            <div>
                {this.props.children}
            </div>
        );
    }
}