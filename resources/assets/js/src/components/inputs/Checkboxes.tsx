import * as React from "react";
import Checkbox from "./Checkbox";
import Label from "../labels/Label";

interface CheckboxChoice {
    key: string;
    value: string;
    selected?: boolean;
    type?: string;
}

interface CheckboxesProps {
    choices: Array<CheckboxChoice>;
}

/**
 * Choice "checkbox" component.
 */
class Checkboxes extends React.Component <CheckboxesProps> {
    constructor(props: CheckboxesProps) {
        super(props);
    }

    /**
     * Render the choices.
     */
    renderChoices() {
        return this.props.choices.map((choice: CheckboxChoice) => {
            if (choice.type && 'group' === choice.type) {
                return (
                    <div className="themosis__choice__group" key={choice.key}>
                        { choice.key }
                    </div>
                );
            }

            return (
                <div className="themosis__choice__item" key={choice.key}>
                    <Checkbox value={choice.value} id={choice.key} />
                    <Label text={choice.key} for={choice.key}/>
                </div>
            );
        });
    }

    /**
     * Render the component.
     */
    render() {
        return (
            <div className="themosis__choice__checkbox">
                { this.renderChoices() }
            </div>
        );
    }
}

export default Checkboxes;