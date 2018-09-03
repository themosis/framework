import Manager from './../../src/components/Manager';
import TextField from './../../src/components/fields/TextField';

test('Text field can be registered to components Manager', () => {
    let manager = new Manager();
    manager.addComponent('themosis.text', TextField);

    expect(manager.hasComponent('themosis.text')).toBeTruthy();
});