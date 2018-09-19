import * as React from "react";
import {Field} from "./common";
import Label from "../labels/Label";
import {isRequired} from "../../helpers";
import Select from "../select/Select";

/**
 * The choice field component.
 */
class ChoiceField extends React.Component <FieldProps> {
    constructor(props: FieldProps) {
        super(props);

        this.onChange = this.onChange.bind(this);
    }

    /**
     * Handle value changes.
     */
    onChange(value: any) {
        this.props.changeHandler(this.props.field.name, this.parseValue(value));
    }

    /**
     * Format value based on field configuration.
     *
     * @return {string|array}
     */
    parseValue(value: string|Array<string>) {
        if (! value.length) {
            return '';
        }

        switch (this.props.field.options.layout) {
            case 'select':
                if (! this.props.field.options.multiple && Array.isArray(value)) {
                    // We handle an array for a single value. Let's return the first element of value.
                    return value.shift();
                }

                // Multiple selection.
                return value;
        }

        return value;
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
                    <Select l10n={{
                        placeholder: this.props.field.options.l10n.placeholder,
                        not_found: this.props.field.options.l10n.not_found
                    }}
                            id={this.props.field.attributes.id}
                            multiple={true}
                            options={[
                                {key: 'None', value: ''},
                                {key: 'Red',value: 'red'},
                                {key: 'Green',value: 'green'},
                                {key: 'Blue',value: 'blue'},
                                {key: 'Cyan',value: 'cyan'},
                                {key: 'Magenta',value: 'magenta'},
                                {key: 'Yellow',value: 'yellow'},
                                {key: 'Black',value: 'black'},
                                {key: 'White',value: 'white'}
                            ]}/>
                </div>
            </Field>
        );
    }
}

export default ChoiceField;