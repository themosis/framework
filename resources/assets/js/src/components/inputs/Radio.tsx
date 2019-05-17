import * as React from "react";
import Label from "../labels/Label";
import classNames from "classnames";

interface RadioProps {
    choices: Array<OptionType>;
    changeHandler?: any;
    className?: string;
    id?: string;
    value?: string;
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
            value: props.value ? props.value : ''
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

        if (this.props.changeHandler) {
            this.props.changeHandler(e.target.value);
        }
    }

    /**
     * Render radio choices.
     */
    renderChoices() {
        return this.props.choices.map((choice: OptionType) => {
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
            <div id={this.props.id} className={classNames('themosis__choice__radio', this.props.className)}>
                { this.renderChoices() }
            </div>
        );
    }
}

export default Radio;