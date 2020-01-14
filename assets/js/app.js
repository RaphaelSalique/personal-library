/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import '../css/app.css'

import 'bulma/css/bulma.css'

import '@fortawesome/fontawesome-free/css/all.css'

import Pagination from './pagination'

const tablesort = require('tablesort')
const bookTable = document.getElementById('books')
const nbBookDisplayed = 20

tablesort(bookTable)

const paginator = new Pagination(bookTable, nbBookDisplayed)
paginator.init()

bookTable.addEventListener('beforeSort', () => {
    paginator.demasqueTous()
})

bookTable.addEventListener('afterSort', () => {
    paginator.init()
})
