import * as React from "react";
import {withState} from "@wordpress/compose";
import {ColorPalette as WordPressColorPalette} from "@wordpress/components";

export const ColorPalette = withState({
    color: '#f00',
})(() => {
    const colors = [
        {name: 'Red', color: '#f00'},
        {name: 'Green', color: '#0f0'}
    ];

    return (
        <WordPressColorPalette colors={colors}/>
    );
});