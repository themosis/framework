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
            let selector = '#' + list[idx] + ' .inside';
                //url = themosisGlobal.api.base_url + 'metabox/' + list[idx] + '?post_id=25';

            ReactDOM.render(<Metabox />, document.querySelector(selector));

            /*axios.get(url)
                .then((response: any) => {
                    let box = response.data;
                    console.log(box);
                })
                .catch((error: any) => {
                    console.log(error);
                });*/
        }

        return this;
    }
}

export default MetaboxFactory;