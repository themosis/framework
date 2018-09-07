import * as React from 'react';
import axios from 'axios';
import MetaboxBody from './MetaboxBody';
import MetaboxFooter from './MetaboxFooter';

interface MetaboxProps {
    id: string;
}

interface MetaboxState {
    fields: Array<FieldType>;
    groups: Array<{}>;
}

/**
 * Metabox container component.
 */
class Metabox extends React.Component <MetaboxProps, MetaboxState> {
    constructor(props: MetaboxProps) {
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
        return (
            <div className="themosis__metabox">
                <MetaboxBody fields={this.state.fields} groups={this.state.groups}/>
                <MetaboxFooter/>
            </div>
        );
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