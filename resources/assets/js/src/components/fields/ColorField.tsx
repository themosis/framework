import * as React from "react";
import {Field} from "./common";
import Label from "../labels/Label";
import {isRequired} from "../../helpers";
import {ColorPalette} from "@wordpress/components";

/**
 * Color field component.
 */
class ColorField extends React.Component <FieldProps> {
    constructor(props: FieldProps) {
        super(props);

        this.onChange = this.onChange.bind(this);
    }

    /**
     * Handle field value changes.
     */
    onChange(color: string) {
        this.props.changeHandler(this.props.field.name, color);
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
                    <ColorPalette onChange={this.onChange}
                                  disableCustomColors={this.props.field.options.disableCustomColors}
                                  value={Array.isArray(this.props.field.value) ? this.props.field.value.shift() : this.props.field.value}
                                  colors={this.props.field.options.colors}/>
                </div>
            </Field>
        );
    }
}

export default ColorField;