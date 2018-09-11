import * as React from "react";
import {Manager} from "../../../index";
import Tabs from "../tabs/Tabs";
import {hasErrors} from "../../helpers";

interface Props {
    fields: Array<FieldType>;
    groups: Array<GroupType>;
    changeHandler: any;
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
        /*
         * Render tabbed fields.
         */
        if (1 < this.props.groups.length) {
            return (
                <div className="themosis__metabox__body">
                    <Tabs items={this.getTabsList()}>
                        { (groupId: string) => {
                            let fields = this.props.fields.filter((field: FieldType) => {
                                return field.options.group === groupId;
                            });

                            return fields.map((field: FieldType) => {
                                const Field = Manager.getComponent(field.component);

                                return (
                                    <Field key={field.name}
                                           field={field}
                                           changeHandler={this.props.changeHandler}/>
                                );
                            });
                        } }
                    </Tabs>
                </div>
            );
        }

        /*
         * Render default metabox (no tabs).
         */
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
        return this.props.fields.map((data:FieldType) => {
            const Field = Manager.getComponent(data.component);
            return (
                <Field key={data.name}
                       field={data}
                       changeHandler={this.props.changeHandler} />
            );
        });
    }

    /**
     * Return a formatted tabs list.
     *
     * @return {Array<TabMenuItem>}
     */
    getTabsList() {
        return this.props.groups.map((group: GroupType) => {
            let groupHasError = false;

            let fields = this.props.fields.filter((field: FieldType) => {
                return group.id === field.options.group;
            });

            for (let idx in fields) {
                if (hasErrors(fields[idx])) {
                    groupHasError = true;
                }
            }

            /**
             * Match <TabMenuItem> definition defined in "Tabs.tsx".
             */
            return {
                id: group.id,
                title: group.title,
                hasError: groupHasError
            };
        });
    }
}

export default MetaboxBody;