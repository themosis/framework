import * as React from "react";
import Label from "../labels/Label";

interface RadioChoice {
    key: string;
    value: string;
    selected?: boolean;
    type?: string;
}

interface RadioProps {
    choices: Array<RadioChoice>;
}

interface RadioState {
    value: string;
}

/**
 * The radio component.
 */
class Radio extends React.Component <RadioProps, RadioState> {
    constructor(props: RadioProps) {
        super(props);

        this.state = {
            value: ''
        };

        this.onChange = this.onChange.bind(this);
    }

    /**
     * Handle radio choice selection.
     */
    onChange (e: any) {
        this.setState({
            value: e.target.value
        });
    }

    /**
     * Render radio choices.
     */
    renderChoices() {
        return this.props.choices.map((choice: RadioChoice) => {
            if (choice.type && 'group' === choice.type) {
                return (
                    <div className="themosis__choice__group" key={choice.key}>
                        { choice.key }
                    </div>
                );
            }

            return (
                <div className="themosis__choice__item" key={choice.key}>
                    <div className="themosis__input__radio">
                        <input type="radio"
                               id={choice.key}
                               value={choice.value}
                               onChange={this.onChange}
                               checked={choice.value === this.state.value}/>
                    </div>
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
            <div className="themosis__choice__radio">
                { this.renderChoices() }
            </div>
        );
    }
}

export default Radio;