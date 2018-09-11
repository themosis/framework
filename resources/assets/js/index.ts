import Manager from "./src/components/Manager";
import MetaboxFactory from "./src/components/MetaboxFactory";
import TextField from "./src/components/fields/TextField";
import TextareaField from "./src/components/fields/TextareaField";
import EmailField from "./src/components/fields/EmailField";
import "./src/styles/metabox.scss";

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

export { manager as Manager };

/**
 * Initialize the Metabox Factory.
 */
const factory = new MetaboxFactory();
factory.make(themosisGlobal.metabox);

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