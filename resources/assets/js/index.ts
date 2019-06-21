import "@babel/polyfill/noConflict";
import axios from "axios";
import Manager from "./src/components/Manager";
import MetaboxFactory from "./src/components/MetaboxFactory";
import TextField from "./src/components/fields/TextField";
import TextareaField from "./src/components/fields/TextareaField";
import EmailField from "./src/components/fields/EmailField";
import PasswordField from "./src/components/fields/PasswordField";
import IntegerField from "./src/components/fields/IntegerField";
import NumberField from "./src/components/fields/NumberField";
import CheckboxField from "./src/components/fields/CheckboxField";
import HiddenField from "./src/components/fields/HiddenField";
import ChoiceField from "./src/components/fields/ChoiceField";
import ColorField from "./src/components/fields/ColorField";
import MediaField from "./src/components/fields/MediaField";
import CollectionField from "./src/components/fields/CollectionField";
import EditorField from "./src/components/fields/EditorField";
import '../sass/index.scss';

/**
 * Axios common settings
 * Identify API requests performed by the framework.
 */
axios.defaults.headers.common['Themosis-Api-Request'] = 1;

/*
 * Initialize the components Manager.
 */
const manager = new Manager();

/**
 * Register Themosis core fields components.
 */
manager.addComponent('themosis.fields.text', TextField);
manager.addComponent('themosis.fields.textarea', TextareaField);
manager.addComponent('themosis.fields.email', EmailField);
manager.addComponent('themosis.fields.password', PasswordField);
manager.addComponent('themosis.fields.integer', IntegerField);
manager.addComponent('themosis.fields.number', NumberField);
manager.addComponent('themosis.fields.checkbox', CheckboxField);
manager.addComponent('themosis.fields.hidden', HiddenField);
manager.addComponent('themosis.fields.choice', ChoiceField);
manager.addComponent('themosis.fields.color', ColorField);
manager.addComponent('themosis.fields.media', MediaField);
manager.addComponent('themosis.fields.collection', CollectionField);
manager.addComponent('themosis.fields.editor', EditorField);

export { manager as Manager };

/**
 * Initialize the Metabox Factory.
 */
if ('undefined' !== typeof themosisGlobal) {
    const factory = new MetaboxFactory();
    factory.make(themosisGlobal.metabox);
}

/**
 * Themosis Library Public API.
 */
const themosis = {
    /**
     * Register a component.
     *
     * @param {string} name
     * @param {Object} component A component reference or any value.
     *
     * @return {Manager} Returns the Components Manager instance.
     */
    register: (name: string, component: any) => {
        return themosis.components.add(name, component);
    },
    /**
     * Provides a public API to handle the registered components.
     */
    components: {
        /**
         * Add and register a new component.
         *
         * @param {string} name
         * @param component
         *
         * @return {Manager}
         */
        add: (name: string, component: any) => {
            return manager.addComponent(name, component);
        },
        /**
         * Return all registered components.
         */
        all: () => {
            return manager.all()
        },
        /**
         * Return the registered component based on given name.
         *
         * @param {string} name
         */
        get: (name: string) => {
            return manager.getComponent(name);
        },
        /**
         * Check if the given component has been registered.
         *
         * @param {string} name
         *
         * @return {boolean}
         */
        has: (name: string) => {
            return manager.hasComponent(name);
        }
    },
    /**
     * Displays a simple message.
     */
    hello: () => {
        return 'Themosis Framework';
    },
};

export default themosis;