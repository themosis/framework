declare module '@wordpress/components' {
    interface Color {
        name: string;
        color: string;
    }

    interface ColorPaletteProps {
        colors?: Array<Color>
        value?: string;
        onChange?: any;
    }

    export const ColorPalette:React.SFC<ColorPaletteProps>;
}