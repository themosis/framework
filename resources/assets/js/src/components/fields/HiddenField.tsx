import * as React from "react";
import {Description, Field} from "./common";
import Label from "../labels/Label";
import {isRequired} from "../../helpers";
import classNames from "classnames";

/**
 * The hidden field component.
 */
class HiddenField extends React.Component <FieldProps> {
    constructor(props: FieldProps) {
        super(props);
    }

    /**
     * Render the component.
     */
    render() {
        return(
            <Field field={this.props.field}>
                <div className="themosis__column__label">
                    <Label required={isRequired(this.props.field)}
                           for={this.props.field.attributes.id}
                           text={this.props.field.label.inner}/>
                </div>
                <div className="themosis__column__content">
                    <div id={this.props.field.attributes.id}
                         className={classNames('themosis__input__hidden', this.props.field.attributes.class)}>
                        {this.props.field.value}
                    </div>
                    { this.props.field.options.info && <Description content={this.props.field.options.info}/> }
                </div>
            </Field>
        );
    }
}

export default HiddenField;