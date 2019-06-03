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
declare interface MediaOptions {
    filesize: string;
    name: string;
    thumbnail: string;
}

declare interface FieldType {
    attributes: {
        checked: string;
        class?: string;
        id: string;
        max: string;
        min: string;
        step: number;
    };
    basename: string;
    component: string;
    data_type: string;
    default: string;
    name: string;
    options: {
        choices: Array<OptionType>;
        colors?: Array<Color>;
        disableCustomColors?: boolean;
        expanded?: boolean;
        group: string;
        id: number;
        info: string;
        items: Array<object>;
        l10n: any;
        layout?: string;
        limit: number;
        media: MediaOptions;
        multiple?: boolean;
        name: string;
        precision: number;
        settings: object;
        settings_js: object;
        thumbnail: string;
        type: string|Array<string>;
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
        messages: Array<string>;
        placeholder: string;
        rules: string | Array<string>;
    };
    value: string | Array<string>
}

declare interface Color {
    name: string;
    color: string;
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

declare interface OptionType {
    key: string;
    value: string;
    selected?: boolean;
    type?: string;
}

/*
|--------------------------------------------------------------------------
| WordPress Global
|--------------------------------------------------------------------------
|
*/
// Media API
declare interface WordPressMediaButton {
    text: string;
    close?: boolean;
}

declare interface WordPressMediaLibrary {
    type: string|Array<string>;
}

declare interface WordPressMediaProps {
    frame?: string;
    multiple?: boolean;
    title?: string;
    button?: WordPressMediaButton;
    library?: WordPressMediaLibrary;
}

declare interface WordPressMedia {
    (options: WordPressMediaProps): any;
}

// Editor API
declare interface WordPressEditorInit {
    (id: string, settings: object): any;
}

declare namespace wp {
    const media: WordPressMedia;
    const oldEditor: {
        initialize: WordPressEditorInit;
    }
}

/*
|--------------------------------------------------------------------------
| TinyMCE Global
|--------------------------------------------------------------------------
|
*/
declare interface TinyMceGet {
    (id?: string): any;
}

declare namespace tinyMCE {
    const get: TinyMceGet;
}