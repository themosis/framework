import * as React from 'react';

/**
 * Field - Simple wrapper for custom fields.
 *
 * @param props
 * @constructor
 */
export function Field (props: any) {
    return (
        <div className="themosis__field">
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