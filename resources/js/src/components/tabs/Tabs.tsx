import * as React from "react";
import classNames from "classnames";

interface TabMenuItem {
    id: string;
    title: string;
    hasError?: boolean;
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
                            <button key={item.id}
                                    type="button"
                                    onClick={() => this.handleClick(item.id)}
                                    className={classNames({'tab__active': this.state.selected === item.id, 'tab__has__errors': item.hasError})}>
                                <span className="shortname">{ item.title.charAt(0) }</span>
                                <span className="fullname">{ item.title }</span>
                            </button>);
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