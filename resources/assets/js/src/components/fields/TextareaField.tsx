import * as React from "react";
import {Description, Field} from "./common";
import Label from "../labels/Label";
import {getErrorsMessages, hasErrors, isRequired, attributes} from "../../helpers";
import Error from "../errors/Error";
import classNames from "classnames";

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
        this.props.changeHandler(this.props.field.name, e.target.value);
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
                              className={classNames('themosis__textarea', this.props.field.attributes.class)}
                              value={this.props.field.value}
                              onChange={this.onChange}
                              {...attributes(this.props.field)}/>
                    { hasErrors(this.props.field) && <Error messages={getErrorsMessages(this.props.field)}/> }
                    { this.props.field.options.info && <Description content={this.props.field.options.info}/> }
                </div>
            </Field>
        );
    }
}

export default TextareaField;