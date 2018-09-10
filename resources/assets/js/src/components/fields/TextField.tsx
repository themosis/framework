import * as React from 'react';
import {Description, Field} from './common';
import Label from '../labels/Label';

/**
 * Text field component.
 */
class TextField extends React.Component <FieldProps> {
    constructor(props: FieldProps) {
        super(props);

        this.onChange = this.onChange.bind(this);
    }

    /**
     * Handle input value changes.
     */
    onChange(e: any) {
        this.props.changeHandler(e.target.name, e.target.value);
    }

    /**
     * Render component UI.
     */
    render() {
        return (
            <Field>
                <div className="themosis__column__label">
                    <Label for={this.props.field.attributes.id} text={this.props.field.label.inner} />
                </div>
                <div className="themosis__column__content">
                    <input id={this.props.field.attributes.id}
                           className="themosis__input"
                           type="text"
                           name={this.props.field.name}
                           value={this.props.field.value}
                           onChange={this.onChange}/>
                    { this.props.field.options.info && <Description content={this.props.field.options.info}/> }
                </div>
            </Field>
        );
    }
}

export default TextField;