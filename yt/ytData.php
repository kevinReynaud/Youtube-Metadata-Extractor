<?php
#variables
$urlYTVideo; #Contendra la url mandada por el cliente
$dataYT = array(); #Arreglo con los datos extraidos del vid yt
$urlYTBase1 = "https://www.youtube.com/watch?v="; #Fragmento constante al compartir una URL de YT
$urlYTBase2 = "https://youtu.be/";#Fragmento constante al compartir desde el boton de comprartir
$urlYTTypeBase = 0; #0 si no es de ninguno de los dos tipos de base de YT URL.
#Yotube tags
$tagTitulo = "<meta property=\"og:title\" content=\"";
$tagDesc = "<meta property=\"og:description\" content=\"";
$tagImgVid = "<meta property=\"og:image\" content=\"";
$tagChanName = '<link itemprop="name" content="';
$tagUrlVid = '<meta property="og:url" content="';
$tagEmbedVid = '<meta property="og:video:url" content="';

  #Comprueba si el metodo request es GET
  if ($_SERVER['REQUEST_METHOD'] == 'GET'){ 
    #Comprueba si se envio el parametro urlVid
    if (isset($_GET['urlVid'])){
      #Guardamos el parametro urlVid
      $urlYTVideo = $_GET['urlVid'];
      #Comprobar que sea una URL Valida
      if ($urlYTVideo !== ""){
        #Comprobamos que sea una URL valida
        if (filter_var($urlYTVideo, FILTER_VALIDATE_URL)){
          #Comprobamos que sea una url de youtube y que tipo, si 1 o 2
          if (strpos($urlYTVideo, $urlYTBase1) !== false){
            $urlYTTypeBase = 1;
          }
          else if (strpos($urlYTVideo, $urlYTBase2) !== false){
            $urlYTTypeBase = 2;
          }
          #Si la URL Es de tipo 1 o 2 hara request sino devuelve codeStatus 3
          if($urlYTTypeBase == 1 || $urlYTTypeBase == 2){
            #Hacemos un request
            $htmlRaw = file_get_contents($urlYTVideo);
            #Si el ID del video esta mal o el video ya no existe, no se encontrara el meta tag <meta property
            if(stripos($htmlRaw, $tagTitulo) !== false){
              #Raspados
              #Titulo
              $tagPosIni = stripos($htmlRaw, $tagTitulo) + strlen($tagTitulo);
              $tagPosFi = stripos($htmlRaw,"\">", $tagPosIni) - $tagPosIni;
              $dataYT += array('title'=> substr($htmlRaw, $tagPosIni, $tagPosFi));
              #Descripcion
              $tagPosIni = stripos($htmlRaw, $tagDesc) + strlen($tagDesc);
              $tagPosFi = stripos($htmlRaw, "\">",$tagPosIni) - $tagPosIni;
              $dataYT += array('description'=> substr($htmlRaw, $tagPosIni, $tagPosFi));
              #Miniatura
              $tagPosIni = stripos($htmlRaw, $tagImgVid) + strlen($tagImgVid);
              $tagPosFi = stripos($htmlRaw, "\">", $tagPosIni) - $tagPosIni;
              $dataYT += array('thumbnail' => substr($htmlRaw, $tagPosIni, $tagPosFi));
              #Nombre Canal
              $tagPosIni = stripos($htmlRaw, $tagChanName) + strlen($tagChanName);
              $tagPosFi = stripos($htmlRaw,'">', $tagPosIni) - $tagPosIni;
              $dataYT += array('channelName' => substr($htmlRaw, $tagPosIni, $tagPosFi));
              #URL Video
              $tagPosIni = stripos($htmlRaw, $tagUrlVid) + strlen($tagUrlVid);
              $tagPosFi = stripos($htmlRaw, '">', $tagPosIni) - $tagPosIni;
              $dataYT += array('urlVid' => substr($htmlRaw, $tagPosIni, $tagPosFi));
              #URL Video Embebido: Se utilizara para poner el video en la pag web
              $tagPosIni = stripos($htmlRaw, $tagEmbedVid) + strlen($tagEmbedVid);
              $tagPosFi = stripos($htmlRaw, '">', $tagPosIni) - $tagPosIni;
              $dataYT += array('urlEmbed' => substr($htmlRaw, $tagPosIni, $tagPosFi));
              #CodeStatus
              $dataYT += array('codeStatus' => 1);
              #Salidas
              echo json_encode($dataYT, JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE);
            }else{
              echo '{"codeStatus": 7}';
            }
          }
          else{
            echo '{"codeStatus": 3}';
          }
        }
        else{
          echo '{"codeStatus": 2}';
        }
      }
      else{
        echo '{"codeStatus": 4}';
      } 
    }
    else{
      echo '{"codeStatus": 7}';
    }
  }
  else{
    echo '{"codeStatus": 5}';
  }
?>