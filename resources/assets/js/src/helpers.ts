/**
 * Return field errors messages.
 *
 * @param field
 *
 * @return {Array<string>}
 */
export const getErrorsMessages = (field: FieldType): Array<string> => {
    if (hasErrors(field)) {
        return field.validation.messages;
    }

    return [];
};

/**
 * Field utility. Check if a field contains errors.
 *
 * @param field
 *
 * @return {boolean}
 */
export const hasErrors = (field: FieldType): boolean => {
    return !!field.validation.messages.length;
};

/**
 * Field utility. Check if a field has a "required" validation rule.
 *
 * @param field
 */
export const isRequired = (field: FieldType): boolean => {
    let rules = field.validation.rules;

    /*
     * Case where rules is an array.
     */
    if (Array.isArray(rules)) {
        for (let idx in rules) {
            if ('required' === rules[idx]) {
                return true;
            }
        }
    }

    /*
     * Default rules is a string.
     */
    return -1 !== rules.indexOf('required');
};

/**
 * General utility. Check if a value is undefined or not.
 *
 * @param value
 *
 * @return {boolean}
 */
export const isUndefined = (value:any): boolean => {
    return 'undefined' === typeof value;
};

/**
 * Javascript version of PHP ucfirst() function.
 * Capitalize first letter of a string.
 *
 * @param text
 *
 * @return {string}
 */
export const ucfirst = (text: string): string => {
    return text.charAt(0).toUpperCase() + text.slice(1);
};

/**
 * Return an object of authorized attributes for the given field.
 *
 * @param field
 *
 * @return {object}
 */
export const attributes = (field: FieldType): object => {
    const ignoredAttributes = ['class', 'id', 'name', 'type', 'value', 'checked'];

    return Object.keys(field.attributes).filter((attributeName: string) => {
        return -1 === ignoredAttributes.indexOf(attributeName);
    }).reduce((obj, key) => {
        obj[key] = field.attributes[key];
        return obj;
    }, {});
};