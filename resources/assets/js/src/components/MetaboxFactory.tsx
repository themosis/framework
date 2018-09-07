import * as React from 'react';
import * as ReactDOM from 'react-dom';
import Metabox from './metabox/Metabox';

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
                selector = '#' + id + ' .inside';

            ReactDOM.render(<Metabox id={id} />, document.querySelector(selector));
        }

        return this;
    }
}

export default MetaboxFactory;