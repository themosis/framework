import React, {useEffect} from "react";
import {L10nProps, StatusProps} from "./MetaboxController";
import MetaboxBody from "./MetaboxBody";
import MetaboxFooter from "./MetaboxFooter";
import Button from "../buttons/Button";
import MetaboxStatus from "./MetaboxStatus";

type MetaboxProps = {
    id?: string;
    fields: Array<FieldType>;
    groups: Array<GroupType>;
    status: StatusProps;
    l10n: L10nProps;
    onChange(name: string, value: string | Array<string>): void;
    onSave(): void;
}

const Metabox = function ({fields, groups, status, l10n, onChange, onSave}: MetaboxProps) {
    useEffect(() => {
        const handleIsSavingPost = () => onSave();

        if (fields.length) {
            window.addEventListener('isSavingPost', handleIsSavingPost);
        } else {
            window.removeEventListener('isSavingPost', handleIsSavingPost);
        }

        return () => window.removeEventListener('isSavingPost', handleIsSavingPost);
    }, [fields]);

    return (
        <div className="themosis__metabox">
            <MetaboxBody fields={fields}
                         groups={groups}
                         changeHandler={onChange}/>
            <MetaboxFooter>
                <Button primary={true}
                        disabled={'saving' === status}
                        clickHandler={onSave}>
                    {l10n.submit}
                </Button>
                {'default' !== status && <MetaboxStatus status={status} label={l10n[status]}/>}
            </MetaboxFooter>
        </div>
    );
};

export default Metabox;