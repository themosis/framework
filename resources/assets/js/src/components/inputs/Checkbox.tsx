import * as React from "react";
import classNames from "classnames";

interface CheckboxProps {
    attributes?: object;
    checked?: boolean;
    changeHandler?: any;
    className?: string;
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
            if (props.changeHandler) {
                props.changeHandler(! state.checked, props.value);
            }

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
                       type="checkbox"
                       className={classNames(this.props.className)}
                       {...this.props.attributes}/>
            </div>
        );
    }
}

export default Checkbox;