import * as React from "react";
import {Description, Field} from "./common";
import Label from "../labels/Label";
import {getErrorsMessages, hasErrors, isRequired, attributes} from "../../helpers";
import Error from "../errors/Error";
import classNames from "classnames";

/**
 * Email field component.
 */
class EmailField extends React.Component <FieldProps> {
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
                           className={classNames('themosis__input', this.props.field.attributes.class)}
                           name={this.props.field.name}
                           value={this.props.field.value}
                           type="email"
                           onChange={this.onChange}
                           {...attributes(this.props.field)}/>
                    { hasErrors(this.props.field) && <Error messages={getErrorsMessages(this.props.field)}/> }
                    { this.props.field.options.info && <Description content={this.props.field.options.info}/> }
                </div>
            </Field>
        );
    }
}

export default EmailField;