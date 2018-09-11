import * as React from 'react';
import Button from '../buttons/Button';

interface TabMenuItem {
    id: string;
    title: string;
}

interface TabsProps {
    items: Array<TabMenuItem>;
    children(id: string): any;
    defaultTab?: string;
}

interface TabsState {
    selected: string;
}

/**
 * Tabs component.
 */
class Tabs extends React.Component <TabsProps, TabsState> {
    constructor(props: TabsProps) {
        super(props);

        this.state = {
            selected: props.defaultTab || props.items.length > 0 ? props.items[0].id : ''
        };

        this.handleClick = this.handleClick.bind(this);
    }

    /**
     * Handle tabs change.
     */
    handleClick(id: string) {
        this.setState({
            selected: id
        });
    }

    /**
     * Render the component.
     */
    render() {
        return (
            <div className="themosis__tabs">
                <div className="themosis__tabs__menu">
                    { this.props.items.map((item) => {
                        return (
                            <Button text={item.title}
                                    key={item.id}
                                    className={this.state.selected === item.id ? 'tab__active' : ''}
                                    clickHandler={() => this.handleClick(item.id)}/>
                        );
                    }) }
                </div>
                { this.props.children && <div className="themosis__tabs__body">
                    { this.props.children(this.state.selected) }
                </div> }
            </div>
        );
    }
}

export default Tabs;