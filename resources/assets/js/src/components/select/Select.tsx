import * as React from "react";
import classNames from "classnames";
import Icon from "../icons/Icon";

interface SelectOption {
    key: string;
    value: string;
    selected?: boolean;
}

interface SelectL10n {
    placeholder?: string;
    not_found?: string;
}

interface SelectProps {
    options: Array<SelectOption>;
    changeHandler?: any;
    id?: string;
    multiple?: boolean;
    l10n?: SelectL10n;
}

interface SelectState {
    open: boolean;
    focus: boolean;
    value: Array<string>;
    selected: Array<string>;
    options: Array<SelectOption>;
    listItems: Array<SelectOption>;
}

/**
 * The select component.
 */
class Select extends React.Component <SelectProps, SelectState> {
    /**
     * Input ref
     */
    private inputRef = React.createRef<HTMLInputElement>();

    constructor(props: SelectProps) {
        super(props);

        this.state = {
            focus: false,
            open: false,
            value: [],
            selected: [],
            options: this.defaultOptions(props.options),
            listItems: this.defaultOptions(props.options)
        };

        this.onBlur = this.onBlur.bind(this);
        this.onFocus = this.onFocus.bind(this);
        this.onInput = this.onInput.bind(this);
        this.onFieldClick = this.onFieldClick.bind(this);
    }

    /**
     * Setup default state options.
     * Add a "selected" value.
     *
     * @param options
     *
     * @return {Array<SelectOption>}
     */
    defaultOptions(options: Array<SelectOption>) {
        return options.map((option) => {
            option.selected = false;
            return option;
        });
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
                options: this.state.options,
                listItems: this.state.options,
                selected: selected.length ? selected : prevState.selected
            };
        });
    }

    /**
     * Handle input event.
     * Filter the options list.
     */
    onInput(e: any) {
        let items = this.props.options.filter((option: SelectOption) => {
            return option.key.toLowerCase().indexOf(e.target.value.toLowerCase()) !== -1;
        });

        this.setState((prevState, props) => {
            return {
                listItems: items,
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
     * Handle field overall focus for the input.
     */
    onFieldClick(e: any) {
        let input = this.inputRef.current,
            target = e.target;

        // Do not focus if we hit a selection tag...
        if (target.classList.contains('themosis__select__tag')) {
            return;
        }

        if (input) {
            input.focus();
            this.setState({
                focus: true,
                open: true
            });
        }
    }

    /**
     * Handle click event on tags. Remove tag from
     * selection and list of values.
     *
     * @param {SelectOption} item
     */
    onTagClick(item: SelectOption) {
        let selected = this.state.selected.slice().filter((selection) => {
            return selection !== item.key;
        });

        let value = this.state.value.slice().filter((val) => {
            return val !== item.value;
        });

        let options = this.state.options.map((option: SelectOption) => {
            if (option.value === item.value) {
                option.selected = false;
            }

            return option;
        });

        this.setState({
            selected: selected,
            value: value,
            options: options
        });
    }

    /**
     * Handle value when an item is selected/clicked.
     */
    onItemSelected(key: string, val: string) {
        let values: Array<string> = this.state.value,
            selected: Array<string> = this.state.selected,
            options: Array<SelectOption> = this.state.options;

        if (! val) {
            if (! this.props.multiple) {
                values = [];
                selected = [];
                options = this.defaultOptions(options);
            }
        } else {
            values = this.parse(this.state.value.slice(), val, !!this.props.multiple);
            selected = this.parse(this.state.selected.slice(), key, !!this.props.multiple);
            options = options.map((option: SelectOption) => {
                if (val === option.value) {
                    option.selected = true;
                }

                return option;
            });
        }

        /*
         * Handle truthy value.
         */
        this.setState({
            value: values,
            selected: selected,
            options: options
        });

        // Send the value to higher component.
        this.props.changeHandler(values);
    }

    /**
     * Find an option object based on its key.
     *
     * @param {string} key
     *
     * @return {SelectOption}
     */
    findOptionByKey(key: string): SelectOption {
        let options = this.state.options.filter((option) => {
            return option.key === key;
        });

        let result = options.shift();

        if (result) {
            return result;
        }

        return {
            key: '',
            value: '',
            selected: false
        };
    }

    /**
     * Parse value and selection.
     * Returned a modified source array with correct added value.
     *
     * @param {Array} source
     * @param {string} val
     * @param {multiple} multiple
     *
     * @return {Array}
     */
    parse(source: Array<string>, val: string, multiple: boolean): Array<string> {
        if (multiple) {
            source.push(val);
        } else {
            source.splice(0, source.length, val);
        }

        return source;
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
        if (! this.state.listItems.length) {
            return (
                <div className="themosis__select__item notfound">
                    <span>{ (this.props.l10n && this.props.l10n.not_found) ? this.props.l10n.not_found : 'Nothing found'}</span>
                </div>
            );
        }

        return this.state.listItems.map((option: SelectOption) => {
            return (
                <div key={option.key}
                     onMouseDown={() => { if (! option.selected) { this.onItemSelected(option.key, option.value) } }}
                     className={classNames('themosis__select__item', {'selected': option.selected})}>
                    <span>{option.key}</span>
                </div>
            );
        });
    }

    /**
     * Render selected options on a "multiple" select component.
     */
    renderMultipleSelection() {
        if (! this.props.multiple) {
            return;
        }

        return this.state.selected.map((item: string) => {
            let option = this.findOptionByKey(item);

            if (! option) {
                return;
            }

            return (
                <a className="themosis__select__tag"
                   onClick={() => { this.onTagClick(option) }}
                   key={option.key}>
                    {option.key}
                    <Icon name="close"/>
                </a>
            );
        });
    }

    /**
     * Render the component.
     */
    render() {
        return (
            <div className={classNames('themosis__select', {'multiple': this.props.multiple, 'selection': this.hasSelection()})}>
                <div className="themosis__select__body">
                    <div className="themosis__select__field" onClick={this.onFieldClick}>
                        { this.renderMultipleSelection() }
                        <input type="text"
                               id={this.props.id}
                               className={classNames('themosis__select__input')}
                               onFocus={this.onFocus}
                               onBlur={this.onBlur}
                               onInput={this.onInput}
                               onKeyDown={this.onKeyDown}
                               ref={this.inputRef}
                               autoComplete="off"/>
                        <div className={classNames('themosis__select__output', {'default': this.shouldShowPlaceholder(), 'open': this.state.open, 'selection': this.hasSelection(), 'multiple': this.props.multiple})}>
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