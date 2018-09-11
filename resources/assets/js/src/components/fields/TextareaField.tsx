import * as React from "react";
import {Description, Field} from "./common";
import Label from "../labels/Label";
import {getErrorsMessages, hasErrors, isRequired} from "../../helpers";
import Error from "../errors/Error";

/**
 * Textarea field component.
 */
class TextareaField extends React.Component <FieldProps> {
    constructor(props: FieldProps) {
        super(props);

        this.onChange = this.onChange.bind(this);
    }

    /**
     * Handle textarea value changes.
     */
    onChange(e: any) {
        this.props.changeHandler(e.target.name, e.target.value);
    }

    /**
     * Render component UI.
     */
    render() {
        return (
            <Field field={this.props.field}>
                <div className="themosis__column__label">
                    <Label required={isRequired(this.props.field)}
                           for={this.props.field.attributes.id}
                           text={this.props.field.label.inner} />
                </div>
                <div className="themosis__column__content">
                    <textarea id={this.props.field.attributes.id}
                              name={this.props.field.name}
                              className="themosis__textarea"
                              value={this.props.field.value}
                              onChange={this.onChange}/>
                    { hasErrors(this.props.field) && <Error messages={getErrorsMessages(this.props.field)}/> }
                    { this.props.field.options.info && <Description content={this.props.field.options.info}/> }
                </div>
            </Field>
        );
    }
}

export default TextareaField;