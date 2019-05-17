import * as React from "react";
import {Description, Field} from "./common";
import Label from "../labels/Label";
import {getErrorsMessages, hasErrors, isRequired} from "../../helpers";
import Select from "../select/Select";
import Checkboxes from "../inputs/Checkboxes";
import Radio from "../inputs/Radio";
import Error from "../errors/Error";

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
    onChange(value: string|Array<string>) {
        this.props.changeHandler(this.props.field.name, value);
    }

    /**
     * Render the right component based on field layout.
     *
     * @param field
     */
    getComponent(field: FieldType) {
        if ('checkbox' === field.options.layout) {
            return (
                <Checkboxes choices={this.props.field.options.choices}
                            value={Array.isArray(this.props.field.value) ? this.props.field.value : []}
                            changeHandler={this.onChange}
                            id={this.props.field.attributes.id}
                            className={this.props.field.attributes.class}/>
            );
        }

        if ('radio' === field.options.layout) {
            return (
                <Radio choices={this.props.field.options.choices}
                       value={'string' === typeof this.props.field.value ? this.props.field.value : ''}
                       changeHandler={this.onChange}
                       id={this.props.field.attributes.id}
                       className={this.props.field.attributes.class}/>
            );
        }

        // Default to "select" component.
        return (
            <Select l10n={{
                placeholder: this.props.field.options.l10n.placeholder,
                not_found: this.props.field.options.l10n.not_found
            }}
                    id={this.props.field.attributes.id}
                    multiple={this.props.field.options.multiple}
                    value={this.props.field.value}
                    changeHandler={this.onChange}
                    options={this.props.field.options.choices}/>
        );
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
                    { this.getComponent(this.props.field) }
                    { hasErrors(this.props.field) && <Error messages={getErrorsMessages(this.props.field)}/> }
                    { this.props.field.options.info && <Description content={this.props.field.options.info}/> }
                </div>
            </Field>
        );
    }
}

export default ChoiceField;