import * as React from "react";
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
    onChange(e: any) {
        this.props.changeHandler(this.props.field.name, e.target.value);
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
                    <input id={this.props.field.attributes.id}
                           className="themosis__input"
                           name={this.props.field.name}
                           value={this.props.field.value}
                           type={this.props.field.attributes.type}
                           onChange={this.onChange}/>
                    {hasErrors(this.props.field) && <Error messages={getErrorsMessages(this.props.field)}/>}
                    {this.props.field.options.info && <Description content={this.props.field.options.info}/>}
                </div>
            </Field>
        );
    }
}

export default DateField;
