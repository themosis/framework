import * as React from 'react';
import {Description, Field} from "./common";
import Label from "../labels/Label";
import {getErrorsMessages, hasErrors, isRequired} from "../../helpers";
import Button from "../buttons/Button";
import Error from "../errors/Error";

interface MediaState {
    frame: any;
    thumbnail: string;
    name: string;
    filesize: string;
}

/**
 * Media Field Component.
 */
class MediaField extends React.Component <FieldProps, MediaState> {
    constructor(props: FieldProps) {
        super(props);

        this.state = {
            frame: null,
            thumbnail: props.field.options.media.thumbnail,
            name: props.field.options.media.name,
            filesize: props.field.options.media.filesize
        };

        this.openMediaLibrary = this.openMediaLibrary.bind(this);
        this.delete = this.delete.bind(this);
    }

    /**
     * Open the media library.
     */
    openMediaLibrary() {
        this.state.frame.open();
    }

    /**
     * Handle media insert/select.
     */
    select() {
        const media = this.getSelection();
        let thumbnail = media.get('icon');

        if ('image' === media.get('type') && 'svg+xml' !== media.get('subtype')) {
            const sizes = media.get('sizes');
            thumbnail = 'undefined' !== typeof sizes['thumbnail'] ? sizes.thumbnail.url : sizes.full.url;
        }

        this.setState({
            thumbnail: thumbnail,
            name: media.get('filename'),
            filesize: media.get('filesizeHumanReadable')
        });

        this.props.changeHandler(this.props.field.name, media.get('id'));
    }

    /**
     * Return selected media from the media library.
     */
    getSelection() {
        return this.state.frame.state().get('selection').first();
    }

    /**
     * Check if there is a media file.
     */
    hasMedia() {
        return this.props.field.value !== '';
    }

    /**
     * Remove the media file.
     */
    delete() {
        this.setState({
            thumbnail: '',
            name: '',
            filesize: ''
        });

        this.props.changeHandler(this.props.field.name, '');
    }

    /**
     * Render the field.
     */
    render() {
        return (
            <Field field={this.props.field}>
                <div className="themosis__column__label">
                    <Label text={this.props.field.label.inner}
                           for={this.props.field.attributes.id}
                           required={isRequired(this.props.field)}/>
                </div>
                <div className="themosis__column__content">
                    { this.renderMedia() }
                    { hasErrors(this.props.field) && <Error messages={getErrorsMessages(this.props.field)}/> }
                    { this.props.field.options.info && <Description content={this.props.field.options.info}/> }
                </div>
            </Field>
        );
    }

    /**
     * Render the media.
     */
    renderMedia() {
        if (! this.hasMedia()) {
            return (
                <div className="themosis__field__media">
                    <Button clickHandler={this.openMediaLibrary}>
                        <span className="icon--media"/>
                        {this.props.field.options.l10n.add}
                    </Button>
                </div>
            );
        }

        return (
            <div className="themosis__field__media">
                <div className="themosis__media__preview">
                    <div className="themosis__media__thumbnail">
                        <img src={this.state.thumbnail} alt={this.state.name}/>
                    </div>
                </div>
                <div className="themosis__media__content">
                    <ul>
                        <li><strong>{this.props.field.options.l10n.name}</strong> {this.state.name} ({this.state.filesize})</li>
                        <li><strong>{this.props.field.options.l10n.id}</strong> {this.props.field.value}</li>
                    </ul>
                    <Button clickHandler={this.delete}>{this.props.field.options.l10n.remove}</Button>
                </div>
            </div>
        );
    }

    /**
     * Setup WordPress Media Library frame.
     */
    componentDidMount() {
        const frame = wp.media({
            frame: 'select',
            multiple: false,
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

export default MediaField;