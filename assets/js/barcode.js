import Quagga from '@ericblade/quagga2';

Quagga.init({
    inputStream: {
        name: "Live",
        type: "LiveStream",
        target: document.querySelector('#interactive')
    },
    decoder: {
        readers: ["ean_reader"]
    }
}, (err) => {
    if (err) {
        console.error(err)
        return
    }
    Quagga.start()
})

Quagga.onDetected((data) => {
    if (data !== null) {
        document.getElementById('form_isbn').value = data['codeResult']['code']
        Quagga.stop()
    }
})
