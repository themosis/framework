import * as React from "react";
import {Description, Field} from "./common";
import Label from "../labels/Label";
import {attributes, getErrorsMessages, hasErrors, isRequired, isUndefined} from "../../helpers";
import Error from "../errors/Error";
import Checkbox from "../inputs/Checkbox";

/**
 * The checkbox field component (single).
 */
class CheckboxField extends React.Component <FieldProps> {
    constructor(props: FieldProps) {
        super(props);

        this.onChange = this.onChange.bind(this);
    }

    /**
     * Handle input value changes.
     */
    onChange(checked: boolean) {
        let value = checked ? 'on' : 'off';
        this.props.changeHandler(this.props.field.name, value);
    }

    /**
     * Check if field has a checked attribute.
     */
    isChecked(): boolean {
        return 'on' === this.props.field.value;
    }

    /*
     * Get the field value if any defined.
     */
    getValue(): string {
        if (isUndefined(this.props.field.value)) {
            return '';
        }

        if (this.props.field.value) {
            return 'on';
        }


        return  'off';
    }

    /**
     * Render the component.
     */
    render() {
        return (
            <Field field={this.props.field}>
                <div className="themosis__column__label">
                    <Label required={isRequired(this.props.field)}
                           for={this.props.field.attributes.id}
                           text={this.props.field.label.inner}/>
                </div>
                <div className="themosis__column__content">
                    <Checkbox changeHandler={this.onChange}
                              id={this.props.field.attributes.id}
                              value={this.getValue()}
                              checked={this.isChecked()}
                              attributes={attributes(this.props.field)}
                              className={this.props.field.attributes.class}/>
                    { hasErrors(this.props.field) && <Error messages={getErrorsMessages(this.props.field)}/> }
                    { this.props.field.options.info && <Description content={this.props.field.options.info}/> }
                </div>
            </Field>
        );
    }
}

export default CheckboxField;