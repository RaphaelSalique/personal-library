import '../css/app.scss'

import 'bulma/css/bulma.css'

import '@fortawesome/fontawesome-free/css/all.css'

let editor = document.querySelector('#book_abstract')
if (editor) {
    ClassicEditor
        .create(document.querySelector('#book_abstract'))
        .catch(error => {
            console.error(error);
        });
}

// Pagination et tri du tableau
import Pagination from './pagination'

const tablesort = require('tablesort')
const tableElement = document.querySelector('table')
const searchfield = document.querySelector('#searchfield')
const pageInitElement = document.querySelector('#pageInit')
if (tableElement !== null) {
    const nbBookDisplayed = 30
    let pageInit = 1
    if (pageInitElement !== null) {
        pageInit = parseInt(pageInitElement.dataset.page)
    }

    tablesort(tableElement)

    const paginator = new Pagination(tableElement, nbBookDisplayed, pageInit)
    paginator.init()

    searchfield.addEventListener('input', () => {
        const searchValue = searchfield.value.toLowerCase();
        paginator.init(searchValue)
    })

    tableElement.addEventListener('beforeSort', () => {
        paginator.demasqueTous()
    })

    tableElement.addEventListener('afterSort', () => {
        const searchValue = searchfield.value.toLowerCase();
        paginator.init(searchValue)
    })
}

// Formulaires avec select multiple
import 'choices.js/src/styles/base.scss'
import 'choices.js/src/styles/choices.scss'

import Choices from 'choices.js'

const selectMultiples = document.querySelectorAll('[multiple="multiple"]')
selectMultiples.forEach((element) => {
    let choices = new Choices(element, {
        shouldSort: true,
        removeItemButton: true
    })
})

// Messages flash
document.addEventListener('DOMContentLoaded', () => {
    (document.querySelectorAll('.notification .delete') || []).forEach(($delete) => {
        let $notification = $delete.parentNode;

        $delete.addEventListener('click', () => {
            $notification.parentNode.removeChild($notification);
        });
    });
});

// Menu burger
document.addEventListener('DOMContentLoaded', () => {

    // Get all "navbar-burger" elements
    const $navbarBurgers = Array.prototype.slice.call(document.querySelectorAll('.navbar-burger'), 0);

    // Check if there are any navbar burgers
    if ($navbarBurgers.length > 0) {
        // Add a click event on each of them
        $navbarBurgers.forEach(el => {
            el.addEventListener('click', () => {

                // Get the target from the "data-target" attribute
                const target = el.dataset.target;
                const $target = document.getElementById(target);

                // Toggle the "is-active" class on both the "navbar-burger" and the "navbar-menu"
                el.classList.toggle('is-active');
                $target.classList.toggle('is-active');

            });
        });
    }

});
