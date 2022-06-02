import React from "react";

type Props = {
    children: React.ReactNode;
};

export default function MetaboxFooter({children}: Props) {
    return (
        <div className="themosis__metabox__footer">
            {children}
        </div>
    );
}
