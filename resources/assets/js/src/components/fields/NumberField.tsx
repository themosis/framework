import * as React from "react";
import {Description, Field} from "./common";
import Label from "../labels/Label";
import {getErrorsMessages, hasErrors, isRequired, attributes} from "../../helpers";
import InputNumber from "../inputs/InputNumber";
import Error from "../errors/Error";

/**
 * The number field component.
 */
class NumberField extends React.Component <FieldProps> {
    constructor(props: FieldProps) {
        super(props);

        this.onChange = this.onChange.bind(this);
    }

    /**
     * Handle input value changes.
     */
    onChange(value: any) {
        this.props.changeHandler(this.props.field.name, value);
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
                    <InputNumber changeHandler={this.onChange}
                                 name={this.props.field.name}
                                 step={this.props.field.attributes.step}
                                 precision={this.props.field.options.precision}
                                 id={this.props.field.attributes.id}
                                 min={this.props.field.attributes.min}
                                 max={this.props.field.attributes.max}
                                 value={this.props.field.value}
                                 className={this.props.field.attributes.class}
                                 attributes={attributes(this.props.field)}/>
                    { hasErrors(this.props.field) && <Error messages={getErrorsMessages(this.props.field)}/> }
                    { this.props.field.options.info && <Description content={this.props.field.options.info}/> }
                </div>
            </Field>
        );
    }
}

export default NumberField;