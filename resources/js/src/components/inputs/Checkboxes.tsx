import * as React from "react";
import Checkbox from "./Checkbox";
import Label from "../labels/Label";
import classNames from "classnames";

interface CheckboxesProps {
    choices: Array<OptionType>;
    changeHandler?: any;
    className?: string;
    id?: string;
    value?: Array<string>;
}

interface CheckboxesState {
    value: Array<string>;
}

/**
 * Choice "checkbox" component.
 */
class Checkboxes extends React.Component <CheckboxesProps, CheckboxesState> {
    constructor(props: CheckboxesProps) {
        super(props);

        this.state = {
            value: props.value ? props.value : []
        };

        this.onChange = this.onChange.bind(this);
    }

    /**
     * Handle checkbox change/checked status.
     */
    onChange(checked: boolean, value: string) {
        let values = this.state.value.slice();

        if (checked) {
            // Add the value.
            values.push(value);
        } else {
            // Remove the value if already defined.
            values = values.filter((val: string) => {
                return val !== value;
            });
        }


        this.setState({
            value: values
        });

        if (this.props.changeHandler) {
            this.props.changeHandler(values);
        }
    }

    /**
     * Check if a checkbox value is checked.
     *
     * @param val
     * @param values
     *
     * @return {boolean}
     */
    isChecked(val: string, values: Array<string>): boolean {
        let result = values.find((value) => {
            return value === val;
        });

        return !!result;
    }

    /**
     * Render the choices.
     */
    renderChoices() {
        return this.props.choices.map((choice: OptionType) => {
            if (choice.type && 'group' === choice.type) {
                return (
                    <div className="themosis__choice__group" key={choice.key}>
                        { choice.key }
                    </div>
                );
            }

            return (
                <div className="themosis__choice__item" key={choice.key}>
                    <Checkbox value={choice.value}
                              id={choice.key}
                              checked={this.isChecked(choice.value, this.state.value)}
                              changeHandler={this.onChange}/>
                    <Label text={choice.key} for={choice.key}/>
                </div>
            );
        });
    }

    /**
     * Render the component.
     */
    render() {
        return (
            <div id={this.props.id} className={classNames('themosis__choice__checkbox', this.props.className)}>
                { this.renderChoices() }
            </div>
        );
    }
}

export default Checkboxes;