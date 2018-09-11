import * as React from "react";
import * as ReactDOM from "react-dom";
import Metabox from "./metabox/Metabox";

interface MetaboxFactoryInterface {
    make(list: string[]): MetaboxFactoryInterface;
}

class MetaboxFactory implements MetaboxFactoryInterface {
    /**
     * Build given metabox UI.
     *
     * @param {Array} list List of metabox unique IDs.
     *
     * @return this
     */
    make(list: string[]): MetaboxFactoryInterface {
        for (let idx in list) {
            let id = list[idx],
                selector = '#' + id + ' .inside',
                elem = document.querySelector(selector);

            /**
             * If element exists:
             * - Reset the styles of the container.
             * - Render the root component.
             */
            if (elem) {
                elem.setAttribute(
                    'style',
                    'margin: 0; padding: 0;'
                );

                ReactDOM.render(<Metabox id={id} />, elem);
            }
        }

        return this;
    }
}

export default MetaboxFactory;