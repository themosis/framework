import * as React from "react";
import classNames from "classnames";

interface ButtonProps {
    clickHandler: any;
    className?: string;
    primary?: boolean;
    disabled?: boolean;
}

/**
 * Button component.
 */
class Button extends React.Component <ButtonProps> {
    static defaultProps = {
        className: 'button',
        primary: false
    };

    constructor(props: ButtonProps) {
        super(props);
    }

    /**
     * Render the component.
     */
    render() {
        return (
            <button className={ classNames(this.props.className, {'button-primary': this.props.primary, 'disabled': this.props.disabled}) }
                    type="button"
                    onClick={this.props.clickHandler}>
                {this.props.children}
            </button>
        );
    }
}

export default Button;