declare module '@wordpress/components' {
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

    interface ColorIndicatorProps {
        className?: string | object;
        colorValue?: string;
    }

    export const ColorIndicator:React.SFC<ColorIndicatorProps>;
}