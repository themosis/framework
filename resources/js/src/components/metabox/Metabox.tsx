import * as React from "react";
import axios, {AxiosError, AxiosResponse} from "axios";
import MetaboxBody from "./MetaboxBody";
import MetaboxFooter from "./MetaboxFooter";
import Button from "../buttons/Button";
import MetaboxStatus from "./MetaboxStatus";
import {hasErrors as hasFieldErrors} from "../../helpers";
import {useEffect, useState} from "react";

type MetaboxProps = {
    id: string;
}

type L10nProps = {
    done: string;
    error: string;
    saving: string;
    submit: string;
};

type StatusProps = keyof Omit<L10nProps, 'submit'> | 'default';

const Metabox = function ({id}: MetaboxProps) {
    const [fields, setFields] = useState<Array<FieldType>>([]);
    const [groups, setGroups] = useState<Array<GroupType>>([]);
    const [l10n, setL10n] = useState<L10nProps>({
        done: 'Saved',
        error: 'Errors',
        saving: 'Saving',
        submit: 'Save'
    });
    const [status, setStatus] = useState<StatusProps>('default');

    let timer: any = undefined;

    useEffect(() => {
        let url = themosisGlobal.api.base_url + 'metabox/' + id + '?post_id=' + themosisGlobal.post.ID;

        axios.get(url)
            .then((response: AxiosResponse) => {
                setFields(response.data.fields.data);
                setGroups(response.data.groups.data);
                setL10n(response.data.l10n);
            })
            .catch((error: AxiosError) => {
                console.log(error);
            });
    }, []);

    const handleChange = (name: string, value: string | Array<string>) => {
        setFields(fields.map((field: FieldType): FieldType => {
            if (name === field.name) {
                field.value = value;
            }

            return field;
        }));
    };

    const handleSave = () => {
        let url = themosisGlobal.api.base_url + 'metabox/' + id + '?post_id=' + themosisGlobal.post.ID;

        /*
         * Change current status to "saving"
         */
        setStatus('saving');

        axios.put(url, {
            fields: fields
        })
            .then((response: AxiosResponse) => {
                /*
                 * First check if there are any errors. Some fields
                 * might have failed the validation.
                 */
                if (hasErrors(response.data.fields.data)) {
                    setFields(response.data.fields.data);
                    setStatus('error');
                } else {
                    setFields(response.data.fields.data);
                    setStatus('done');
                }

                timer = setTimeout(clearStatus, 5000);
            })
            .catch((error: AxiosError) => {
                /*
                 * Reset metabox status to default
                 * and log the error to the console.
                 */
                setStatus('default');

                console.log(error.message);
            });
    };

    const hasErrors = (fields: Array<FieldType>): boolean => {
        for (let idx in fields) {
            let field = fields[idx];

            if (hasFieldErrors(field)) {
                return true;
            }
        }

        return false;
    };

    const clearStatus = () => {
        setStatus('default');
        clearTimeout(timer);
    }

    return (
        <div className="themosis__metabox">
            <MetaboxBody fields={fields}
                         groups={groups}
                         changeHandler={handleChange}/>
            <MetaboxFooter>
                <Button primary={true}
                        disabled={'saving' === status}
                        clickHandler={handleSave}>
                    {l10n.submit}
                </Button>
                {'default' !== status && <MetaboxStatus status={status} label={l10n[status]}/>}
            </MetaboxFooter>
        </div>
    );
};

export default Metabox;