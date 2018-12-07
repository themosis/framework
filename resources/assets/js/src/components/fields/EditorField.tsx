import * as React from "react";
import {Description, Field} from "./common";
import Label from "../labels/Label";
import {getErrorsMessages, hasErrors, isRequired} from "../../helpers";
import Error from "../errors/Error";

class EditorField extends React.Component <FieldProps> {
    constructor(props: FieldProps) {
        super(props);
    }

    /**
     * Render the editor component.
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
                    <textarea id={this.props.field.attributes.id}
                              className={'wp-editor'}
                              defaultValue={this.props.field.value}
                              name={this.props.field.name}/>
                    {hasErrors(this.props.field) && <Error messages={getErrorsMessages(this.props.field)}/>}
                    {this.props.field.options.info && <Description content={this.props.field.options.info}/>}
                </div>
            </Field>
        );
    }

    componentDidMount() {
        let settings = {...this.props.field.options.settings_js, ...{
            'tinymce': {
                'init_instance_callback': (editor: any) => {
                    editor.on('blur', () => {
                        this.props.changeHandler(this.props.field.name, tinyMCE.get(this.props.field.attributes.id).getContent());
                    });
                }
            }
        }};

        wp.oldEditor.initialize(this.props.field.attributes.id, settings);
    }
}

export default EditorField;