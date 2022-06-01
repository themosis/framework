import * as React from "react";

interface LabelProps {
    text: string;
    for?: string;
    required?: boolean;
}

/**
 * Label component.
 */
class Label extends React.Component <LabelProps> {
    static defaultProps = {
        required: false
    };

    constructor(props: LabelProps) {
        super(props);
    }

    /**
     * Render the component.
     */
    render() {
        return (
            <label className="themosis__field__label"
                   htmlFor={this.props.for}>
                {this.props.text} {this.props.required && <span className="required">*</span>}
            </label>
        );
    }
}

export default Label;