import Quagga from 'quagga';

Quagga.init({
    inputStream: {
        name: "Live",
        type: "LiveStream",
        target: document.querySelector('#interactive')
    },
    decoder: {
        readers: ["ean"]
    }
}, (err) => {
    if (err) {
        console.error(err)
        return
    }
    Quagga.start()
})

Quagga.onProcessed((data) => {
    document.getElementById('result_strip').innerHTML = 'et de un'
})

Quagga.onDetected((data) => {
    document.getElementById('result_strip').innerHTML = 'et de deux'
})
