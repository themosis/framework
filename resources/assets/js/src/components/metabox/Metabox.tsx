import * as React from 'react';
import axios from 'axios';
import { Manager } from '../../../index';

interface Props {
    id: string;
}

interface State {
    fields: Array<{}>;
    groups: Array<{}>;
}

/**
 * Metabox container component.
 */
class Metabox extends React.Component <Props, State> {
    constructor(props: Props) {
        super(props);
        this.state = {
            fields: [],
            groups: []
        };
    }

    /**
     * Render component UI.
     */
    render() {
        if (1 < this.state.groups.length) {
            return ('Tabbed metabox');
        }

        return (
            <div>{ this.renderDefaultMetabox() }</div>
        );
    }

    /**
     * Render a default metabox.
     */
    renderDefaultMetabox() {
        return this.state.fields.map((data:any) => {
            const Field = Manager.getComponent(data.component);
            return (
                <Field key={data.name} />
            );
        });
    }

    /**
     * Fetch metabox data.
     */
    componentDidMount() {
        let url = themosisGlobal.api.base_url + 'metabox/' + this.props.id + '?post_id=25';

        axios.get(url)
            .then((response: any) => {
                let box = response.data;
                this.setState({
                    fields: box.fields.data,
                    groups: box.groups.data
                });
            })
            .catch((error: any) => {
                console.log(error);
            });
    }
}

export default Metabox;