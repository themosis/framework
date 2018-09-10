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
        messages: Array<string>;
        placeholder: string;
        rules: string;
    };
    value: string | Array<string>
}

// Field {field} component property.
declare interface FieldProps {
    field: FieldType;
    changeHandler: any;
}