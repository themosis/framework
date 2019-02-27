import * as React from "react";
import axios, {AxiosError, AxiosResponse} from "axios";
import MetaboxBody from "./MetaboxBody";
import MetaboxFooter from "./MetaboxFooter";
import Button from "../buttons/Button";
import MetaboxStatus from "./MetaboxStatus";
import {hasErrors} from "../../helpers";

interface MetaboxProps {
    id: string;
}

interface MetaboxState {
    fields: Array<FieldType>;
    groups: Array<GroupType>;
    l10n: {
        done: string;
        error: string;
        saving: string;
        submit: string;
    };
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
            l10n: {
                done: 'Saved',
                error: 'Errors',
                saving: 'Saving',
                submit: 'Save'
            },
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
                    <Button primary={true}
                            disabled={'saving' === this.state.status}
                            clickHandler={this.save}>
                        {this.state.l10n.submit}
                    </Button>
                    { 'default' !== this.state.status && <MetaboxStatus status={this.state.status}
                                                                        label={this.state.l10n[this.state.status]}/> }
                </MetaboxFooter>
            </div>
        );
    }

    /**
     * Handle onChange events for each field.
     * This updates the "state" of our metabox fields values.
     */
    change(name: string, value: string|Array<string>) {
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
                /*
                 * First check if there are any errors. Some fields
                 * might have failed the validation.
                 */
                if (this.hasErrors(response.data.fields.data)) {
                    this.setState({
                        fields: response.data.fields.data,
                        status: 'error'
                    });
                } else {
                    this.setState({
                        fields: response.data.fields.data,
                        status: 'done'
                    });
                }

                this.timer = setTimeout(this.clearStatus, 5000);
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
     * Check if the metabox has errors.
     */
    hasErrors(fields: Array<FieldType>): boolean {
        for (let idx in fields) {
            let field = fields[idx];

            if (hasErrors(field)) {
                return true;
            }
        }

        return false;
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
                    groups: response.data.groups.data,
                    l10n: response.data.l10n
                });
            })
            .catch((error: AxiosError) => {
                console.log(error);
            });
    }
}

export default Metabox;