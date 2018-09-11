import * as React from "react";

interface ErrorsProps {
    messages: Array<string>;
}

/**
 * Field error component.
 */
class Error extends React.Component <ErrorsProps> {
    constructor(props: ErrorsProps) {
        super(props);
    }

    /**
     * Render the component.
     */
    render() {
        return (
            <div className="themosis__field__errors">
                <ul>
                    {this.renderMessages()}
                </ul>
            </div>
        );
    }

    /**
     * Render the field error messages.
     */
    renderMessages() {
        return this.props.messages.map((msg: string, idx: number) => {
            return (<li key={idx}>{msg}</li>);
        });
    }
}

export default Error;