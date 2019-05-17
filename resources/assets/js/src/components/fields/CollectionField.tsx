import * as React from "react";
import {Description, Field} from "./common";
import Label from "../labels/Label";
import {getErrorsMessages, hasErrors, isRequired} from "../../helpers";
import Error from "../errors/Error";
import Button from "../buttons/Button";
import classNames from "classnames";
import {arrayMove, SortableContainer, SortableElement} from "react-sortable-hoc";

interface CollectionState {
    frame: any;
    items: Array<any>;
    selected: Array<number>;
}

/**
 * Collection field component.
 */
class CollectionField extends React.Component <FieldProps, CollectionState> {
    constructor(props: FieldProps) {
        super(props);

        // Media library models are BackboneJS based. But when coming from our own
        // API, it is not. So we should avoid BackboneJS functions when
        // manipulating the models.
        this.state = {
            frame: null,
            items: props.field.options.items.length ? props.field.options.items : [],
            selected: []
        };

        this.onSortEnd = this.onSortEnd.bind(this);
        this.openMediaLibrary = this.openMediaLibrary.bind(this);
        this.removeAll = this.removeAll.bind(this);
    }

    /**
     * Check if collection has items.
     *
     * @return boolean
     */
    hasItems() {
        return this.props.field.value.length;
    }

    /**
     * Open the media library.
     */
    openMediaLibrary() {
        this.state.frame.open();
    }

    /**
     * Handle media library selection.
     */
    select() {
        const collection = this.getSelection();
        let items = collection.models;

        // Limit selection and total items.
        if (this.props.field.options.limit) {
            let limit = this.props.field.options.limit - this.state.items.length,
                end = (limit < 0) ? 0 : limit;

            items = items.slice(0, end);
        }

        // Filter selected items. Remove any selected items if already exists in the collection.
        items = items.filter((item: any) => {
            let ids = this.state.items.map((item: any) => {
                return item.id;
            });

            return -1 === ids.indexOf(item.id);
        });

        // Push new selection to existing list of items.
        items = this.state.items.slice().concat(items);

        // Update state.
        this.setState({
            items: items
        });

        // Send value.
        this.props.changeHandler(this.props.field.name, items.map((item: any) => {
            return item.id;
        }));
    }

    /**
     * Return media library selected items.
     *
     * @return Array
     */
    getSelection() {
        return this.state.frame.state('library').get('selection');
    }

    /**
     * Hightlight an item from the collection.
     *
     * @param id
     */
    toggleItem(id: number) {
        let selected = this.state.selected.slice(),
            item = selected.filter((itemID: number) => {
                return id === itemID;
            }).shift();

        if (item) {
            // Remove it from the selection.
            selected = selected.filter((itemID: number) => {
                return id !== itemID;
            });
        } else {
            // Add it to the selection.
            selected.push(id);
        }

        this.setState({
            selected: selected
        });
    }

    /**
     * Check if an item is selected.
     *
     * @param id
     */
    isSelected(id: number) {
        return this.state.selected.filter((itemID: number) => {
            return id === itemID;
        }).shift();
    }

    /**
     * Remove a selected item from the collection.
     *
     * @param id
     */
    removeItem(id: number) {
        let selected = this.state.selected.filter((itemID: number) => {
            return id !== itemID;
        });

        let items = this.state.items.filter((item: any) => {
            return id !== item.id;
        });

        this.setState({
            items: items,
            selected: selected
        });

        this.props.changeHandler(this.props.field.name, items.map((item: any) => {
            return item.id;
        }));
    }

    /**
     * Remove all selected items.
     */
    removeAll() {
        let items = this.state.items.filter((item: any) => {
            return -1 === this.state.selected.indexOf(item.id);
        });

        this.setState({
            items: items,
            selected: []
        });

        this.props.changeHandler(this.props.field.name, items.map((item: any) => {
            return item.id;
        }));
    }

    /**
     * Update state on sort end event.
     */
    onSortEnd({oldIndex, newIndex}: {oldIndex: number, newIndex: number}) {
        const items = arrayMove(this.state.items, oldIndex, newIndex);

        this.setState({
            items: items
        });

        this.props.changeHandler(this.props.field.name, items.map((item: any) => {
            return item.id;
        }));
    }

    /**
     * Render the field.
     */
    render() {
        return (
            <Field field={this.props.field}>
                <div className="themosis__column__label">
                    <Label required={isRequired(this.props.field)}
                           for={this.props.field.attributes.id}
                           text={this.props.field.label.inner}/>
                </div>
                <div className="themosis__column__content">
                    {this.renderCollection()}
                    {hasErrors(this.props.field) && <Error messages={getErrorsMessages(this.props.field)}/>}
                    {this.props.field.options.info && <Description content={this.props.field.options.info}/>}
                </div>
            </Field>
        );
    }

    /**
     * Render the collection.
     */
    renderCollection() {
        return (
            <div className="themosis__field__collection">
                <div className={classNames('themosis__collection', {'show': this.hasItems()})}>
                    {this.renderItems()}
                </div>
                <div className="themosis__collection__buttons">
                    <Button className={classNames('button', 'themosis__collection__button--add')}
                            clickHandler={this.openMediaLibrary}>
                        <span className="icon--media"/>
                        {this.props.field.options.l10n.add}
                    </Button>
                    <Button className={classNames('themosis__collection__button--remove', {'show': this.state.selected.length})}
                            clickHandler={this.removeAll}>
                        {this.props.field.options.l10n.remove}
                    </Button>
                </div>
            </div>
        );
    }

    /**
     * Render list and individual items.
     */
    renderItems() {
        const SortableItem = SortableElement(({item, thumbnail}: {item: any, thumbnail: string}) => {
            return (
                <div key={item.id}
                     className={classNames('themosis__collection__item', {'selected': this.isSelected(item.id)})}
                     onClick={() => {this.toggleItem(item.id)}}>
                    <div className="themosis__collection__item__thumbnail">
                        <img src={thumbnail} alt={item.attributes.filename}/>
                        <div className="themosis__collection__item__overlay">
                            <p>{item.attributes.filename}</p>
                        </div>
                    </div>
                    <Button className="themosis__collection__item__check"
                            clickHandler={() => {this.removeItem(item.id)}}>
                        <span className="icon"/>
                    </Button>
                </div>
            );
        });

        const SortableList = SortableContainer(({items}: {items: Array<any>}) => {
            return (
                <div className="themosis__collection__list">
                    {items.map((item, index) => {
                        let thumbnail = item.attributes.icon;

                        if ('image' === item.attributes.type && 'svg+xml' !== item.attributes.subtype) {
                            const sizes = item.attributes.sizes;
                            thumbnail = 'undefined' !== typeof sizes['thumbnail'] ? sizes.thumbnail.url : sizes.full.url;
                        }

                        return (
                            <SortableItem key={`item-${index}`}
                                          index={index}
                                          item={item}
                                          thumbnail={thumbnail}/>
                        );
                    })}
                </div>
            );
        });

        return (
            <SortableList items={this.state.items}
                          axis={'xy'}
                          distance={12}
                          onSortEnd={this.onSortEnd} />
        );
    }

    componentDidMount() {
        const frame = wp.media({
            frame: 'select',
            multiple: true,
            title: this.props.field.options.l10n.title,
            button: {
                text: this.props.field.options.l10n.button,
                close: true
            },
            library: {
                type: this.props.field.options.type
            }
        });

        frame.on('select', this.select.bind(this));

        this.setState({
            frame: frame
        });
    }
}

export default CollectionField;