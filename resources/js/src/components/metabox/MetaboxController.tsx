import React, {useEffect, useState} from "react";
import Metabox from "./Metabox";
import axios, {AxiosError, AxiosResponse} from "axios";
import {hasErrors as hasFieldErrors} from "../../helpers";

type Props = {
    id: string;
};

export type L10nProps = {
    done: string;
    error: string;
    saving: string;
    submit: string;
};

export type StatusProps = keyof Omit<L10nProps, 'submit'> | 'default';

export default function MetaboxController({id}: Props) {
    const [fields, setFields] = useState<Array<FieldType>>([]);
    const [groups, setGroups] = useState<Array<GroupType>>([]);
    const [l10n, setL10n] = useState<L10nProps>({
        done: 'Saved',
        error: 'Errors',
        saving: 'Saving',
        submit: 'Save'
    });
    const [status, setStatus] = useState<StatusProps>('default');

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

    let timer: any = undefined;

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

    return (
        <Metabox
            id={id}
            fields={fields}
            groups={groups}
            status={status}
            l10n={l10n}
            onChange={handleChange}
            onSave={handleSave}/>
    );
};