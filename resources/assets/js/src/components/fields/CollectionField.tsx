import * as React from "react";
import {Description, Field} from "./common";
import Label from "../labels/Label";
import {getErrorsMessages, hasErrors, isRequired} from "../../helpers";
import Error from "../errors/Error";
import Button from "../buttons/Button";
import classNames from "classnames";

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

        this.state = {
            frame: null,
            items: [],
            selected: []
        };

        this.openMediaLibrary = this.openMediaLibrary.bind(this);
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

        if (this.props.field.options.limit) {
            let limit = this.props.field.options.limit - this.state.items.length,
                end = (limit < 0) ? 0 : limit;

            items = items.slice(0, end);
        }

        let selected = items.map((item: any) => {
            return item.get('id');
        });

        this.setState({
            items: items,
            selected: selected
        });

        this.props.changeHandler(this.props.field.name, selected);
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
                    <div className="themosis__collection__list">
                        {this.renderItems()}
                    </div>
                </div>
                <div className="themosis__collection__buttons">
                    <Button className={classNames('button', 'themosis__collection__button--add')}
                            clickHandler={this.openMediaLibrary}>
                        <span className="icon--media"/>
                        Add Media
                    </Button>
                    <Button className={classNames('themosis__collection__button--remove')}
                            clickHandler={() => {}}>
                        Remove Selected
                    </Button>
                </div>
            </div>
        );
    }

    /**
     * Render individual item.
     */
    renderItems() {
        return this.state.items.map((item: any) => {
            let thumbnail = item.get('icon');

            if ('image' === item.get('type') && 'svg+xml' !== item.get('subtype')) {
                const sizes = item.get('sizes');
                thumbnail = sizes.thumbnail.url;
            }

            return (
                <div key={item.get('id')} className="themosis__collection__item">
                    <div className="themosis__collection__item__thumbnail">
                        <img src={thumbnail} alt={item.get('filename')}/>
                        <div className="themosis__collection__item__overlay">
                            <p>{item.get('filename')}</p>
                        </div>
                    </div>
                    <Button className="themosis__collection__item__check"
                            clickHandler={() => {}}>
                        <span className="icon"/>
                    </Button>
                </div>
            );
        });
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