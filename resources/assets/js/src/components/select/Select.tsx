import * as React from "react";
import classNames from "classnames";

interface SelectOption {
    key: string;
    value: string;
}

interface SelectProps {
    options: Array<SelectOption>;
    multiple?: boolean;
    placeholder?: string;
}

interface SelectState {
    open: boolean;
    focus: boolean;
    value: Array<string>;
    selected: Array<string>;
    options: Array<SelectOption>
}

/**
 * The select component.
 */
class Select extends React.Component <SelectProps, SelectState> {
    constructor(props: SelectProps) {
        super(props);

        this.state = {
            focus: false,
            open: false,
            value: [],
            selected: [],
            options: props.options
        };

        this.onBlur = this.onBlur.bind(this);
        this.onFocus = this.onFocus.bind(this);
        this.onInput = this.onInput.bind(this);
    }

    /**
     * Handle input focus event. Open the select list.
     */
    onFocus() {
        this.setState({
            focus: true,
            open: true
        });
    }

    /**
     * Handle input blue event. May close the select list.
     */
    onBlur(e:any) {
        // Clear input value if any and reset options list to default.
        e.target.value = '';

        this.setState({
            focus: false,
            open: false,
            options: this.props.options
        });
    }

    /**
     * Handle input event.
     * Filter the options list.
     */
    onInput(e: any) {
        let options = this.props.options.filter((option: SelectOption) => {
            return option.key.toLowerCase().indexOf(e.target.value.toLowerCase()) !== -1;
        });

        this.setState((prevState, props) => {
            return {
                options: options,
                selected: props.multiple ? prevState.selected : []
            };
        });
    }

    /**
     * Handle value.
     */
    onItemSelected(key: string, val: string) {
        /*
         * Handle if value is falsy.
         */
        if (! val) {
            return this.setState((prevState, props) => {
                let values:Array<string> = [],
                    selected:Array<string> = [];

                if (props.multiple && prevState.value.length) {
                    values = prevState.value;
                    selected = prevState.selected;
                }

                return {
                    value: values,
                    selected: selected
                };
            });
        }

        /*
         * Handle truthy value.
         */
        this.setState((prevState, props) => {
            let values = prevState.value.slice();
            let selected = prevState.selected.slice();

            if (props.multiple) {
                values.push(val);
                selected.push(key);
            } else {
                values.splice(0, values.length, val);
                selected.splice(0, selected.length, key);
            }

            return {
                value: values,
                selected: selected
            };
        });
    }

    /**
     * Check if there is a selection.
     */
    hasSelection(): boolean {
        return !!this.state.selected.length;
    }

    /**
     * Return the selected options for display only.
     */
    getSelection(): string {
        /*
         * If there is no value, let's check after the placeholder.
         */
        if (this.shouldShowPlaceholder()) {
            return this.props.placeholder ? this.props.placeholder : '';
        }

        let selection = this.state.selected;

        /*
         * Handle single value selection.
         */
        if (selection.length && !this.props.multiple) {
            return selection.toString();
        }

        /*
         * If "multiple", the selection text is always empty.
         */
        return '';
    }

    /**
     * Check if the component should show the placeholder text.
     *
     * @return {boolean}
     */
    shouldShowPlaceholder(): boolean {
        if (this.state.open) {
            return false;
        }

        return !!(!this.state.value.length && this.props.placeholder);
    }

    /**
     * Render the list options.
     */
    renderOptions() {
        return this.state.options.map((option: SelectOption) => {
            return (
                <div key={option.key}
                     onMouseDown={() => { this.onItemSelected(option.key, option.value) }}
                     className="themosis__select__item">
                    <span>{option.key}</span>
                </div>
            );
        });
    }

    /**
     * Render the component.
     */
    render() {
        return (
            <div className="themosis__select">
                <div className="themosis__select__body">
                    <input type="text"
                           className={classNames('themosis__select__input')}
                           onFocus={this.onFocus}
                           onBlur={this.onBlur}
                           onInput={this.onInput}
                           autoComplete="off"/>
                    <div className={classNames('themosis__select__output', {'default': this.shouldShowPlaceholder(), 'open': this.state.open, 'selection': this.hasSelection()})}>
                        {this.getSelection()}
                    </div>
                    <div className={classNames('themosis__select__list', {'open': this.state.open})}>
                        { this.renderOptions() }
                    </div>
                </div>
            </div>
        );
    }
}

export default Select;