require('./bootstrap');


var lozad = require('lozad');
window.onload = (event) => {
    const observer = lozad('.lozad');
    observer.observe();
};
