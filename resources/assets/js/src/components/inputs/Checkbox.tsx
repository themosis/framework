import * as React from "react";

interface CheckboxProps {
    checked: boolean;
    changeHandler: any;
    value?: string;
    id?: string;
}

interface CheckboxState {
    checked: boolean;
}

/**
 * The checkbox input component.
 */
class Checkbox extends React.Component <CheckboxProps, CheckboxState> {
    constructor(props: CheckboxProps) {
        super(props);

        this.state = {
            checked: props.checked || false
        };

        this.onChange = this.onChange.bind(this);
    }

    /**
     * Handle checkbox value change event.
     */
    onChange() {
        this.setState((state, props) => {
            this.props.changeHandler(! state.checked, props.value);

            return {
                checked: ! state.checked
            };
        });
    }

    /**
     * Render the component.
     */
    render() {
        return (
            <div className="themosis__input__checkbox">
                <input onChange={this.onChange}
                       checked={this.state.checked}
                       value={this.props.value}
                       id={this.props.id}
                       type="checkbox"/>
            </div>
        );
    }
}

export default Checkbox;