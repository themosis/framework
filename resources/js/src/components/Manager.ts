interface ManagerInterface {
    /**
     * Add a component to the list.
     *
     * @param {string} name
     * @param {any} component
     */
    addComponent(name: string, component: any): this;

    /**
     * Check if the component has been registered.
     *
     * @param {string} name
     *
     * @return {boolean}
     */
    hasComponent(name: string): boolean;

    /**
     * Return a component based on given short name.
     *
     * @param {string} name
     *
     * @return {any}
     */
    getComponent(name: string): any;

    /**
     * Return the list of registered components.
     *
     * @return {Object}
     */
    all(): Object;
}

class Manager implements ManagerInterface {
    /**
     * List of registered components.
     */
    protected components: any = {};

    /**
     * Add a component to the list.
     *
     * @param {string} name
     * @param {any} component
     *
     * @return {this}
     */
    addComponent(name: string, component: any): this {
        this.components[name] = component;

        return this;
    }

    /**
     * Check if the component has been registered.
     *
     * @param {string} name
     *
     * @return {boolean}
     */
    hasComponent(name: string): boolean {
        const keys = Object.keys(this.components);

        for (let i = 0; i < keys.length; i++) {
            if (name === keys[i]) {
                return true;
            }
        }

        return false;
    }

    /**
     * Return a component based on given short name.
     *
     * @param {string} name
     *
     * @return {any}
     */
    getComponent(name: string): any|null {
        if (this.hasComponent(name)) {
            return this.components[name];
        }

        throw new ReferenceError(`The [${name}] component can not be found.`);
    }

    /**
     * Return the list of registered components.
     *
     * @return {Object}
     */
    all(): Object {
        return this.components;
    }
}

export default Manager;