/**
 * Return field errors messages.
 *
 * @param field
 *
 * @return {Array<string>}
 */
export const getErrorsMessages = (field: FieldType): Array<string> => {
    if (hasErrors(field)) {
        return field.validation.messages[field.name];
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
    return !!field.validation.messages[field.name];
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