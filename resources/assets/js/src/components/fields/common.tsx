import * as React from "react";
import {hasErrors} from "../../helpers";
import classNames from "classnames";

/**
 * Field - Simple wrapper for custom fields.
 *
 * @param props
 * @constructor
 */
interface FieldWrapperProps {
    field: FieldType;
    children: any;
    className?: string | object;
}

export function Field (props: FieldWrapperProps) {
    return (
        <div className={classNames('themosis__field', {'has__errors': hasErrors(props.field)}, props.className)}>
            {props.children}
        </div>
    );
}

/**
 * Description - Render a field description.
 */
interface DescriptionProps {
    content: string;
}

export function Description (props: DescriptionProps) {
    return (
        <div className="themosis__description" dangerouslySetInnerHTML={{__html: props.content}}/>
    );
}