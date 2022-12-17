require('jsdom-global')(undefined, {
    url: 'https://my-site.com'
});

window.Date = Date;

let meta = window.document.createElement('meta');
meta.name = 'csrf-token';
meta.content = '123456';
window.document.getElementsByTagName('head')[0].appendChild(meta)

global.document = window.document;
