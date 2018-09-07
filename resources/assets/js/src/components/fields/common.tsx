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
};