import * as React from 'react';
import {Field} from "./common";
import Label from "../labels/Label";
import {isRequired} from "../../helpers";
import Button from "../buttons/Button";

interface MediaState {
    frame: any;
    thumbnail: string;
    name: string;
    filesize: string;
    id: number;
}

/**
 * Media Field Component.
 */
class MediaField extends React.Component <FieldProps, MediaState> {
    constructor(props: FieldProps) {
        super(props);

        this.state = {
            frame: null,
            thumbnail: '',
            name: '',
            filesize: '',
            id: 0
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
            thumbnail = sizes.thumbnail.url;
        }

        this.setState({
            thumbnail: thumbnail,
            name: media.get('filename'),
            filesize: media.get('filesizeHumanReadable'),
            id: media.get('id')
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
        return this.state.id !== 0;
    }

    /**
     * Remove the media file.
     */
    delete() {
        this.setState({
            thumbnail: '',
            name: '',
            id: 0,
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
                    {this.renderMedia()}
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
                        <li><strong>{this.props.field.options.l10n.id}</strong> {this.state.id}</li>
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
            title: 'Insert Media',
            button: {
                text: 'Insert',
                close: true
            },
            library: {
                type: ['image', 'application']
            }
        });

        frame.on('select', this.select.bind(this));

        this.setState({
            frame: frame
        });
    }
}

export default MediaField;