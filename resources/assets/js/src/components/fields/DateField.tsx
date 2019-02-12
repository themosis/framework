import * as React from "react";
import {DatePicker} from "@wordpress/components";
import {Description, Field} from "./common";
import Label from "../labels/Label";
import {getErrorsMessages, hasErrors, isRequired} from "../../helpers";
import Error from "../errors/Error";

/**
 * Date field component.
 */
class DateField extends React.Component <FieldProps> {

    constructor(props: FieldProps) {
        super(props);

        this.onChange = this.onChange.bind(this);
    }

    /**
     * Handle input value change.
     */
    onChange(value: string) {
        this.props.changeHandler(this.props.field.name, value);
    }

    /**
     * Return field value.
     */
    getValue(): string {
        if (!this.props.field.value) {
            return '';
        }
        return Array.isArray(this.props.field.value) ? this.props.field.value[0] : this.props.field.value;
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
                    <DatePicker currentDate={this.getValue()} onChange={this.onChange}/>
                    {hasErrors(this.props.field) && <Error messages={getErrorsMessages(this.props.field)}/>}
                    {this.props.field.options.info && <Description content={this.props.field.options.info}/>}
                </div>
            </Field>
        );
    }
}

export default DateField;
