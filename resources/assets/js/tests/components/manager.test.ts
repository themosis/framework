import Manager from '../../src/components/Manager';

test('Components manager is instance of Manager', () => {
    let manager = new Manager();
    expect(manager).toBeInstanceOf(Manager);
});

test('Component is added', () => {
    let manager = new Manager();
    manager.addComponent('custom', 'some-value');
    manager.addComponent('name-with-dashes', 'isitworking');
    manager.addComponent('name_with_underscores', 'shouldwork');

    expect(manager.hasComponent('custom')).toBeTruthy();
    expect(manager.getComponent('custom')).toBe('some-value');

    expect(manager.hasComponent('name-with-dashes')).toBeTruthy();
    expect(manager.getComponent('name-with-dashes')).toBe('isitworking');

    expect(manager.hasComponent('name_with_underscores')).toBeTruthy();
    expect(manager.getComponent('name_with_underscores')).toBe('shouldwork');
});

test('Component cannot be found throw an error', () => {
    let manager = new Manager();

    expect(manager.hasComponent('random')).toBeFalsy();
    expect(() => {
        manager.getComponent('doesnotexist')
    }).toThrow();
});

test('Manager returns empty object if no components registered', () => {
    let manager = new Manager();

    expect(manager.all()).toEqual({});
    expect(manager.hasComponent('anything')).toBeFalsy();
});

test('Manager returns all registered components', () => {
    let manager = new Manager();

    manager.addComponent('one', 10);
    manager.addComponent('two', 20);
    manager.addComponent('three',30);

    expect(manager.all()).toEqual({
        one: 10,
        two: 20,
        three: 30
    });
});

test('Manager overrides components with same name', () => {
    let manager = new Manager();

    manager.addComponent('first', 1);
    manager.addComponent('second', 2);
    manager.addComponent('first', 3);

    expect(Object.keys(manager.all()).length).toBe(2);
    expect(manager.getComponent('first')).toBe(3);
});
