import * as React from 'react';
import axios, {AxiosError, AxiosResponse} from 'axios';
import MetaboxBody from './MetaboxBody';
import MetaboxFooter from './MetaboxFooter';
import Button from "../buttons/Button";
import MetaboxStatus from './MetaboxStatus';

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
    /**
     * Status timeout reference.
     */
    protected timer: any;

    constructor(props: MetaboxProps) {
        super(props);
        this.state = {
            fields: [],
            groups: [],
            status: 'default'
        };

        this.change = this.change.bind(this);
        this.save = this.save.bind(this);
        this.clearStatus = this.clearStatus.bind(this);
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
                    { 'default' !== this.state.status && <MetaboxStatus status={this.state.status} label={themosisGlobal.l10n.metabox[this.state.status]}/> }
                    <Button text={themosisGlobal.l10n.metabox['submit']}
                            primary={true}
                            disabled={'saving' === this.state.status}
                            clickHandler={this.save} />
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

        /*
         * Change current status to "saving"
         */
        this.setState({
            status: 'saving'
        });

        axios.put(url, {
            fields: this.state.fields
        })
            .then((response: AxiosResponse) => {
                this.setState({
                    fields: response.data.fields.data,
                    status: 'done'
                });

                this.timer = setTimeout(this.clearStatus, 3000);
            })
            .catch((error: AxiosError) => {
                /*
                 * Reset metabox status to default
                 * and log the error to the console.
                 */
                this.setState({
                    status: 'default'
                });

                console.log(error.message);
            });
    }

    /**
     * Clear metabox footer status.
     */
    clearStatus() {
        this.setState({
            status: 'default'
        });

        clearTimeout(this.timer);
    }

    /**
     * Fetch metabox data.
     * Initialize fields.
     */
    componentDidMount() {
        let url = themosisGlobal.api.base_url + 'metabox/' + this.props.id + '?post_id=' + themosisGlobal.post.ID;

        axios.get(url)
            .then((response: AxiosResponse) => {
                this.setState({
                    fields: response.data.fields.data,
                    groups: response.data.groups.data
                });
            })
            .catch((error: AxiosError) => {
                console.log(error);
            });
    }
}

export default Metabox;