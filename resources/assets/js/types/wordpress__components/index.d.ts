declare module '@wordpress/components' {
    // ColorIndicator
    interface ColorIndicatorProps {
        className?: string | object;
        colorValue?: string;
    }

    export const ColorIndicator:React.SFC<ColorIndicatorProps>;

    // ColorPalette
    interface Color {
        name: string;
        color: string;
    }

    type ColorPaletteChangeHandler = (color: string) => void;

    interface ColorPaletteProps {
        colors?: Array<Color>;
        disableCustomColors?: boolean;
        value?: string;
        onChange?: ColorPaletteChangeHandler;
        className?: string | object;
    }

    export const ColorPalette:React.SFC<ColorPaletteProps>;

    // Draggable
    interface onDragCallback {
        ():void;
    }

    interface DraggableProps {
        elementId: string;
        transferData: object;
        onDragStart?: onDragCallback;
        onDragEnd?: onDragCallback;
    }

    export const Draggable:React.SFC<DraggableProps>;

    // DropZone
    export const DropZone:React.SFC;

    // DropZoneProvider
    export const DropZoneProvider:React.SFC;
}