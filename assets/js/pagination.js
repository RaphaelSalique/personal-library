export default class Pagination {
    constructor(tableau, nbDisplayed, pageInit)
    {
        this.tableau = tableau
        this.nbDisplayed = nbDisplayed
        this.pageInit = pageInit
        this.tbody = this.tableau.querySelector('tbody')
        this.total = this.tbody.childElementCount
        this.ul = null
        this.tfoot = null
    }
    demasqueSurIntervalle(min)
    {
        const max = min + this.nbDisplayed
        const trs = [...this.tbody.children].filter(child => !child.classList.contains('not-selected'))
        for (let i = 0; i < trs.length; i++) {
            trs[i].classList.add('is-hidden')
            if (i >= min && i < max) {
                trs[i].classList.remove('is-hidden')
            }
        }

    }
    demasqueTous()
    {
        let total = [...this.tbody.children].filter(child => !child.classList.contains('not-selected')).length
        const trs = [...this.tbody.children].filter(child => !child.classList.contains('not-selected'))
        for (let i = 0; i < total; i++) {
            trs[i].classList.remove('is-hidden')
        }
        if (this.tfoot) {
            this.tableau.removeChild(this.tfoot)
            this.tfoot = null
        }

    }
    marqueCurrent(current)
    {
        Array.from(this.ul.children).forEach((li, index) => {
            let a = li.querySelector('a')
            a.classList.remove('is-current')
            if (index === current) {
                a.classList.add('is-current')
            }
        })
    }
    init(searchValue = '')
    {
        const haveSearch = searchValue.length > 0;
        this.total = 0;
        [...this.tbody.children].forEach(child => {
            let text = child.innerText.toLowerCase()
            child.classList.add('not-selected')
            child.classList.remove('is-hidden')
            if (text.includes(searchValue) || !haveSearch) {
                child.classList.remove('not-selected')
                this.total++
            }
        })
        if (this.tableau.querySelector('#total')) {
            this.tableau.querySelector('#total').innerText = this.total
        }
        if (this.tfoot) {
            this.tableau.removeChild(this.tfoot)
            this.tfoot = null
        }

        if (this.total > this.nbDisplayed) {
            this.demasqueSurIntervalle(0)
            this.tfoot = document.createElement('tfoot')
            const tr = document.createElement('tr')
            const th = document.createElement('th')
            th.setAttribute('colspan', 8)
            const nbPages = Math.ceil(this.total / this.nbDisplayed)
            const nav = document.createElement('nav')
            nav.classList.add('pagination')
            nav.setAttribute('role', 'navigation')
            nav.setAttribute('aria-label', 'pagination')
            this.ul = document.createElement('ul')
            this.ul.classList.add('pagination-list')
            for (let i = 0; i < nbPages; i++) {
                let li = document.createElement('li')

                let a = document.createElement('a')
                a.classList.add('pagination-link')
                if (i === 0) {
                    a.classList.add('is-current')
                }
                a.innerHTML = "" + (i + 1)
                a.setAttribute('data-min', '' + i)
                a.addEventListener('click', (e) => {
                    let min = parseInt(e.target.getAttribute('data-min'))
                    let demarrage = min * this.nbDisplayed
                    this.demasqueSurIntervalle(demarrage)
                    this.marqueCurrent(min)
                    e.preventDefault()
                })
                li.appendChild(a)
                this.ul.appendChild(li)
                if (this.pageInit === (i + 1)) {
                    a.click()
                }
            }
            nav.appendChild(this.ul)
            th.appendChild(nav)
            tr.appendChild(th)
            this.tfoot.appendChild(tr)
            this.tableau.appendChild(this.tfoot)
        }
    }
}
