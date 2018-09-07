import * as React from 'react';

/**
 * Text field component.
 */
class TextField extends React.Component
{
    /**
     * Render component UI.
     */
    render() {
        return (
            <div className="themosis__field">
                <div className="themosis__column__label">
                    <label>Label</label>
                </div>
                <div className="themosis__column__content">
                    <input type="text"/>
                </div>
            </div>
        );
    }
}

export default TextField;