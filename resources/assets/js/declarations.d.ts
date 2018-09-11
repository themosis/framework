/*
|--------------------------------------------------------------------------
| Global Declarations
|--------------------------------------------------------------------------
|
*/
declare namespace themosisGlobal {
    const metabox: Array<string>;
}

declare namespace themosisGlobal.api {
    const base_url: string;
}

declare namespace themosisGlobal.post {
    const ID: number;
    const post_author: number;
    const post_date: string;
    const post_date_gmt: string;
    const post_content: string;
    const post_title: string;
    const post_excerpt: string;
    const post_status: string;
    const comment_status: string;
    const ping_status: string;
    const post_password: string;
    const post_name: string;
    const to_ping: string;
    const pinged: string;
    const post_modified: string;
    const post_modified_gmt: string;
    const post_content_filtered: string;
    const post_parent: number;
    const guid: string;
    const menu_order: number;
    const post_type: string;
    const post_mime_type: string;
    const comment_count: number;
    const filter: string;
}

/*
|--------------------------------------------------------------------------
| Fields Declarations
|--------------------------------------------------------------------------
|
*/
declare interface FieldType {
    attributes: {
        id: string
    };
    basename: string;
    component: string;
    data_type: string;
    default: string;
    name: string;
    options: {
        group: string;
        info: string;
    };
    label: {
        inner: string;
        attributes: {
            for?: string;
        };
    };
    theme: string;
    type: string;
    validation: {
        errors: boolean;
        messages: object;
        placeholder: string;
        rules: string | Array<string>;
    };
    value: string | Array<string>
}

// Field {field} component property.
declare interface FieldProps {
    field: FieldType;
    changeHandler: any;
}

declare interface GroupType {
    id: string;
    theme?: string;
    title: string;
}
