import * as React from "react";
import Icon from "../icons/Icon";

interface MetaboxStatusProps {
    status: string;
    label: string;
}

/**
 * Metabox status component.
 */
class MetaboxStatus extends React.Component <MetaboxStatusProps> {
    constructor(props: MetaboxStatusProps) {
        super(props);
    }

    /**
     * Render the component.
     */
    render() {
        return (
            <div className="themosis__metabox__status">
                <span className="themosis__metabox__status__icon">
                    <Icon name={this.props.status}/>
                </span>
                <p className="themosis__metabox__status__text">{this.props.label}</p>
            </div>
        );
    }
}

export default MetaboxStatus;