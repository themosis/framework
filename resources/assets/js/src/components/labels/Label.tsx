import * as React from 'react';

interface LabelProps {
    text: string;
    for?: string;
}

/**
 * Label component.
 */
class Label extends React.Component <LabelProps> {
    constructor(props: LabelProps) {
        super(props);
    }

    /**
     * Render the component.
     */
    render() {
        return (
            <label className="themosis__field__label" htmlFor={this.props.for}>
                {this.props.text}
                {this.props.children}
            </label>
        );
    }
}

export default Label;