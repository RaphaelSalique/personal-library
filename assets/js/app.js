/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

import '../css/app.scss'

import 'bulma/css/bulma.css'

import '@fortawesome/fontawesome-free/css/all.css'

// Pagination et tri du tableau
import Pagination from './pagination'

const tablesort = require('tablesort')
const tableElement = document.querySelector('table')
if (tableElement !== null) {
    const nbBookDisplayed = 20

    tablesort(tableElement)

    const paginator = new Pagination(tableElement, nbBookDisplayed)
    paginator.init()

    tableElement.addEventListener('beforeSort', () => {
        paginator.demasqueTous()
    })

    tableElement.addEventListener('afterSort', () => {
        paginator.init()
    })
}

// Messages flash
document.addEventListener('DOMContentLoaded', () => {
    (document.querySelectorAll('.notification .delete') || []).forEach(($delete) => {
        let $notification = $delete.parentNode;

        $delete.addEventListener('click', () => {
            $notification.parentNode.removeChild($notification);
        });
    });
});
