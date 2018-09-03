import React from 'react';
import ReactDOM from 'react-dom';

declare const Themosis:any;

const metabox = document.querySelector('#properties .inside');
const Field = Themosis.components.get('themosis.fields.text');

ReactDOM.render(<Field />, metabox);
