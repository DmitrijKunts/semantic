require('./bootstrap');


var lozad = require('lozad');
window.onload = (event) => {
    const observer = lozad('.lozad', {
        load: function(el) {
            el.data = el.getAttribute('data-data');
            el.srcset = el.getAttribute('data-srcset');
        }
    });
    observer.observe();
};
