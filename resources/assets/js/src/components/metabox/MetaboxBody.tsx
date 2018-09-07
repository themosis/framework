import * as React from 'react';
import {Manager} from '../../../index';

interface Props {
    fields: Array<{}>;
    groups: Array<{}>;
}

/**
 * Metabox body.
 * Handle the UI for the main section of the metabox.
 */
class MetaboxBody extends React.Component <Props> {
    constructor(props: Props) {
        super(props);
    }

    /**
     * Render the component.
     */
    render() {
        if (1 < this.props.groups.length) {
            return ('Tabbed metabox');
        }

        return (
            <div className="themosis__metabox__body">
                { this.renderDefaultMetabox() }
            </div>
        );
    }

    /**
     * Render a default metabox.
     */
    renderDefaultMetabox() {
        return this.props.fields.map((data:any) => {
            const Field = Manager.getComponent(data.component);
            return (
                <Field key={data.name} />
            );
        });
    }
}

export default MetaboxBody;