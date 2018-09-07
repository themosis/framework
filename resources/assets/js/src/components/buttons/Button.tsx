import * as React from 'react';
import classNames from 'classnames';

interface ButtonProps {
    text: string;
    className?: string;
    primary?: boolean;
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
            <button className={ classNames(this.props.className, {'button-primary': this.props.primary}) }>
                {this.props.text}
            </button>
        );
    }
}

export default Button;