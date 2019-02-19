declare module '@wordpress/components' {
    // ColorIndicator
    interface ColorIndicatorProps {
        className?: string | object;
        colorValue?: string;
    }

    export const ColorIndicator:React.FunctionComponent<ColorIndicatorProps>;

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

    export const ColorPalette:React.FunctionComponent<ColorPaletteProps>;

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

    export const Draggable:React.FunctionComponent<DraggableProps>;

    // DropZone
    export const DropZone:React.FunctionComponent;

    // DropZoneProvider
    export const DropZoneProvider:React.FunctionComponent;

    // DatePicker
    interface onChange {
        (value:string): void;
    }
    interface DatePicker{
        currentDate?: string;
        onChange?: onChange;
    }
    export const DatePicker:React.ComponentClass<DatePicker>;
}
