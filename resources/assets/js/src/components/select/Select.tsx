import * as React from "react";
import classNames from "classnames";
import Icon from "../icons/Icon";

interface SelectOption {
    key: string;
    value: string;
}

interface SelectL10n {
    placeholder?: string;
    not_found?: string;
}

interface SelectProps {
    options: Array<SelectOption>;
    id?: string;
    multiple?: boolean;
    l10n?: SelectL10n;
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
     * Handle input blur event. May close the select list.
     */
    onBlur(e:any) {
        // Clear input value if any and reset options list to default.
        e.target.value = '';

        this.setState((prevState, props) => {
            let selected:Array<string> = [];

            /*
             * If nothing is selected but there was an initial value, let's
             * bring back the selection.
             */
            if (prevState.value.length && ! prevState.selected.length) {
                // Loop through all values and bring back selected keys.
                selected = prevState.value.map((value) => {
                    let opt = props.options.filter((option:SelectOption) => {
                        return option.value === value;
                    }).shift();

                    return opt ? opt.key : '';
                }).filter(key => key);
            }

            return {
                focus: false,
                open: false,
                options: this.props.options,
                selected: selected.length ? selected : prevState.selected
            };
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
     * Handle input key down event.
     */
    onKeyDown(e: any) {
        /*
         * Check if user is pressing the "ESC" key.
         */
        if ('Escape' === e.key) {
            e.target.blur();
        }
    }

    /**
     * Handle value when an item is selected/clicked.
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
        if (this.shouldShowPlaceholder() && this.props.l10n) {
            return this.props.l10n.placeholder ? this.props.l10n.placeholder : '';
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

        return !!(!this.state.value.length && this.props.l10n && this.props.l10n.placeholder);
    }

    /**
     * Render the list options.
     */
    renderOptions() {
        /*
         * Handle no found options.
         */
        if (! this.state.options.length) {
            return (
                <div className="themosis__select__item notfound">
                    <span>{ (this.props.l10n && this.props.l10n.not_found) ? this.props.l10n.not_found : 'Nothing found'}</span>
                </div>
            );
        }

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
                    <div className="themosis__select__field">
                        <input type="text"
                               id={this.props.id}
                               className={classNames('themosis__select__input')}
                               onFocus={this.onFocus}
                               onBlur={this.onBlur}
                               onInput={this.onInput}
                               onKeyDown={this.onKeyDown}
                               autoComplete="off"/>
                        <div className={classNames('themosis__select__output', {'default': this.shouldShowPlaceholder(), 'open': this.state.open, 'selection': this.hasSelection()})}>
                            {this.getSelection()}
                        </div>
                        <Icon name="arrow_down"/>
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