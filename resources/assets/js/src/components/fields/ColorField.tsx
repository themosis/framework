import * as React from "react";
import {Field} from "./common";
import Label from "../labels/Label";
import {isRequired} from "../../helpers";
import {ColorPalette} from "../color-palette/ColorPalette";

/**
 * Color field component.
 */
class ColorField extends React.Component <FieldProps> {
    constructor(props: FieldProps) {
        super(props);
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
                    <ColorPalette/>
                </div>
            </Field>
        );
    }
}

export default ColorField;