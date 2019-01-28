require('jsdom-global')();

window.Date = Date;
//global.Date = window.Date
let meta = window.document.createElement('meta');
meta.name = 'csrf-token';
meta.content = '123456';
window.document.getElementsByTagName('head')[0].appendChild(meta)


global.document = window.document;
global.expect = require('expect');
