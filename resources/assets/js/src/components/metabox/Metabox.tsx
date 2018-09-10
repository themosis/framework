import * as React from 'react';
import axios, {AxiosError, AxiosResponse} from 'axios';
import MetaboxBody from './MetaboxBody';
import MetaboxFooter from './MetaboxFooter';
import Button from "../buttons/Button";

interface MetaboxProps {
    id: string;
}

interface MetaboxState {
    fields: Array<FieldType>;
    groups: Array<{}>;
    status: string;
}

/**
 * Metabox container component.
 */
class Metabox extends React.Component <MetaboxProps, MetaboxState> {
    constructor(props: MetaboxProps) {
        super(props);
        this.state = {
            fields: [],
            groups: [],
            status: ''
        };

        this.change = this.change.bind(this);
        this.save = this.save.bind(this);
    }

    /**
     * Render component UI.
     */
    render() {
        return (
            <div className="themosis__metabox">
                <MetaboxBody fields={this.state.fields}
                             groups={this.state.groups}
                             changeHandler={this.change}/>
                <MetaboxFooter>
                    <Button text="Save Changes" primary={true} clickHandler={this.save} />
                </MetaboxFooter>
            </div>
        );
    }

    /**
     * Handle onChange events for each field.
     * This updates the "state" of our metabox fields values.
     */
    change(name: string, value: any) {
        const fields:Array<FieldType> = this.state.fields.map((field: FieldType): FieldType => {
            if (name === field.name) {
                field.value = value;
            }

            return field;
        });

        this.setState({
            fields: fields
        });
    }

    /**
     * Save metabox fields data.
     */
    save() {
        let url = themosisGlobal.api.base_url + 'metabox/' + this.props.id + '?post_id=' + themosisGlobal.post.ID;

        axios.put(url, {
            fields: this.state.fields
        })
            .then((response:AxiosResponse) => {
                console.log(response);
            })
            .catch((error: AxiosError) => {
                console.log(error);
            });
    }

    /**
     * Fetch metabox data.
     * Initialize fields.
     */
    componentDidMount() {
        let url = themosisGlobal.api.base_url + 'metabox/' + this.props.id + '?post_id=' + themosisGlobal.post.ID;

        axios.get(url)
            .then((response: AxiosResponse) => {
                let box = response.data;
                this.setState({
                    fields: box.fields.data,
                    groups: box.groups.data
                });
            })
            .catch((error: AxiosError) => {
                console.log(error);
            });
    }
}

export default Metabox;