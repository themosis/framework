import * as React from "react";
import Icon from "../icons/Icon";
import {isUndefined} from "../../helpers";
import classNames from "classnames";

interface InputNumberProps {
    value: any;
    name: string;
    step: number;
    precision: number;
    attributes?: object;
    className?: string;
    id?: string;
    min?: string;
    max?: string;
    changeHandler(value: any): void;
}

/**
 * Input number component.
 */
class InputNumber extends React.Component <InputNumberProps> {
    constructor(props: InputNumberProps) {
        super(props);

        this.onBlur = this.onBlur.bind(this);
        this.onChange = this.onChange.bind(this);
        this.onClick = this.onClick.bind(this);
    }

    /**
     * Handle input value change.
     */
    onChange(e:any) {
        let rawValue:string = e.target.value,
            dotPosition: number = rawValue.indexOf('.'),
            value:string|number = '';

        if (dotPosition !== -1) {
            value = rawValue;
        } else {
            value = this.parseValue(rawValue);
        }

        this.props.changeHandler(this.parseRange(value));
    }

    /**
     * Handle blur event on input.
     * Format the number based on given properties.
     */
    onBlur(e:any) {
        let value = this.parseValue(e.target.value);

        this.props.changeHandler(this.parseRange(this.setPrecision(value, this.props.precision)));
    }

    /**
     * Check if a given value is in the range.
     *
     * @param {string|number} value
     *
     * @return {string|number}
     */
    parseRange(value: string|number): string|number {
        if ('undefined' !== typeof this.props.min && value < this.props.min) {
            value = this.props.min;
        }

        if ('undefined' !== typeof this.props.max && value > this.props.max) {
            value = this.props.max;
        }

        return value;
    }

    /**
     * Handle click events to increase or decrease input value.
     */
    onClick(num: number) {
        let value = this.parseValue(this.props.value) + num;

        this.props.changeHandler(this.parseRange(this.setPrecision(value, this.props.precision)));
    }

    /*
     * Parse given value and return it as a number.
     */
    parseValue(value: string): number {
        let val = parseFloat(value);

        return isNaN(val) ? 0 : val;
    }

    /*
     * Set the value precision.
     */
    setPrecision(num: number, precision: number): string {
        return num.toFixed(precision);
    }

    /**
     * Return the step property.
     */
    getStep(): number {
        let step = this.props.step;
        return isUndefined(step) ? 1 : step;
    }

    /**
     * Render the component.
     */
    render() {
        return (
            <div className="themosis__input__number">
                <button className="button__minus"
                        onClick={() => { this.onClick(-1 * this.getStep()) }}
                        type="button">
                    <Icon name="minus"/>
                </button>
                <input onChange={this.onChange}
                       onBlur={this.onBlur}
                       name={this.props.name}
                       value={this.props.value}
                       id={this.props.id}
                       type="text"
                       className={classNames(this.props.className)}
                       {...this.props.attributes}/>
                <button className="button__plus"
                        onClick={() => { this.onClick(this.getStep()) }}
                        type="button">
                    <Icon name="plus"/>
                </button>
            </div>
        );
    }
}

export default InputNumber;