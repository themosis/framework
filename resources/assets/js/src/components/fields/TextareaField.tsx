import * as React from 'react';

/**
 * Textarea field component.
 */
class TextareaField extends React.Component {
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
                    <textarea></textarea>
                </div>
            </div>
        );
    }
}

export default TextareaField;