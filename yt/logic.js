//Variables
const myHeaders = new Headers(); //Headers para el fetch request
var parametros; //Contendra los parametros mandados al servidor
//document.getElementById
var formulario = document.getElementById('ytUrlForm');
var ytDataView = document.getElementById("ytDataView");
var contPrin = document.getElementById('contPrin');
var urlBox = document.getElementById('urlBox');
var loadBar = document.getElementById('loadBar');
//Logica
//Capturar accion de pegado
urlBox.addEventListener('paste', function(_e){
    /*formulario.preventDefault();*/
    loadBar.style.display = "block";
    let urlPaste = (_e.clipboardData || window.clipboardData).getData('text');
    urlBox.value = urlPaste;//Por alguna razon chrome elimina el value al usar blur, asi que lo recuperamos por codigo 
    urlPaste = `?urlVid=${urlPaste}`
    ytReqDib(urlPaste);
    urlBox.blur();
})
/*Request y dibujar los datos de respuesta en el DOM
Ponemos un escucha al precionar submit*/
formulario.addEventListener('submit', function(e){
    e.preventDefault(); //Evita que el navegador ejeute el request
    urlBox.blur();
    loadBar.style.display = "block";
    var datos = new FormData(formulario); //Datos del formulario html a una variable
    params = `?urlVid=${datos.get('urlVid')}`; //Obtenemos el parametro
    ytReqDib(params);
})

function ytReqDib(parametros){
    console.log('Parametros: ' + parametros)
    
    fetch(`ytData.php${parametros}`,{
        method: 'GET',
        headers: myHeaders,
        mode: 'no-cors',
        cache: 'no-cache',
    })
    .then(response => response.json())
    .then( data => {
        console.log(data)
        contPrin.style.height = "80%";
        loadBar.style.display = "none";
        switch (data.codeStatus){
            case 1:
                contPrin.style.height = "auto";
                ytDataView.innerHTML = '';
                ytDataView.innerHTML += `<div id="ytFrame"><iframe width="100%" height="100%" src="${data.urlEmbed}" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe></div>`;
                ytDataView.innerHTML += `<p id="ytTittle">${data.title}</p>`;
                ytDataView.innerHTML += `<div id="channelBut"><a href="${data.urlVid}">${data.channelName}</a></div>`;
                ytDataView.innerHTML += `<br>${data.description}`;
                //ytDataView.innerHTML += `<img src="${data.thumbnail}">`; //Miniatura del video oculata para poner un frame
                //ytDataView.innerHTML = data.codeStatus;
                break;
            case 2:
                ytDataView.innerHTML = "<b>La Url no es valida</b>";
                break;
            case 3:
                ytDataView.innerHTML = "<b>La URL no es de un video de Youtube</b>"
                break;
            case 4:
                ytDataView.innerHTML = "<b>El parametro URL esta vacio</b>"
                break;
            case 5:
                ytDataView.innerHTML = "<b>No es un metodo de peticion valido</b>"
                break;
            case 6:
                ytDataView.innerHTML = "<b>El video no existe o la ID del video esta mal escrita</b>"
                break;
            case 7:
                ytDataView.innerHTML = "<b>No agrego paramametro urlVid, esta mal escrito o su video ya no existe</b>"
                break;
            default: ytDataView.innerHTML = "<b>Error desconocido</b>"
        } 
    })
}
