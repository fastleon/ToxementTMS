<?php
/*
*numSeguimiento -> si esta vacio ("") no se mostrara la seccion de fechas y sus iconos
*$fechasSeguimiento -> array de 6 fechas en el siguiente orden:
*   fecha estimada de entrega
*   (fecha) En proceso logístico
*   (fecha) Entrega a transportadora
*   (fecha) En transito
*   (fecha) En reparto
*   (fecha) Entregado
*/

function generarHTML($numSeguimiento, $fechasSeguimiento, $urlSoporte) {
    /*valores por defecto cuando no se recibe numSeguimiento*/
    $visibilidadSeccionFechas = "hidden";
    $heightSeccionFechas = "100px"; 
    if ($numSeguimiento != ""){
        $visibilidadSeccionFechas = "visible";
        $heightSeccionFechas = "auto";
    }
    $estadosEnvio = [];
    foreach($fechasSeguimiento as $fecha){
        if ($fecha == ""){
            array_push($estadosEnvio, "off");
        }else{
            array_push($estadosEnvio, "on");
        }
    }
    /*Metodo para saber que linea usar basandose en la ultima fecha no vacia*/
    $fechaActiva = 1;
    $i = count($estadosEnvio)-1;
    do{
        if ($estadosEnvio[$i] == "on"){
            $fechaActiva = $i;
            break;
        }
        $i--;
    }while($i >= 1);

    /*precaucion array tenga menos de 6 fechas*/
    while( count($estadosEnvio)<6 ){
        array_push($estadosEnvio, "off");
    }
    /*Si urlSoporte viene null se deja vacio*/
    if (is_null($urlSoporte)){
        $urlSoporte = array();
    }

    $html = <<<HTML
        <!DOCTYPE html>
        <html lang="es">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <link rel="stylesheet" href="css/style_seguimiento.css">
            <link rel="preconnect" href="https://fonts.googleapis.com">
            <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
            <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@500;600;700;800&display=swap" rel="stylesheet">
            <link rel="icon" type="image/png" href="img/favicon.ico">
            <script src="https://www.google.com/recaptcha/api.js" async defer></script>
            <script>
                //Referente al Captcha
                function onSubmit(token) { 
                    document.getElementById("consulta-seguimiento").submit();
                }
            </script>
            <script>
                //Referente del boton limpiar
                function resetVisibility() {
                    document.getElementById('sec-estado-envio').style.visibility = 'hidden'; 
                    document.getElementById('sec-estado-envio').style.height = "100px";
                }
            </script>
            <script>
                //Slides de la ventana modal
                let slideIndex = 1;
                actualSlide(slideIndex);
                // control adelante y atras
                function moverSlides(n) {
                actualSlide(slideIndex += n);
                }
                // Control puntos inferiores
                function currentSlide(n) {
                actualSlide(slideIndex = n);
                }
                //cambiar de slide
                function actualSlide(n) {
                    let i;
                    let slides = document.getElementsByClassName("slides");
                    let dots = document.getElementsByClassName("dot");
                    if (n > slides.length) {slideIndex = 1}
                    if (n < 1) {slideIndex = slides.length}
                    for (i = 0; i < slides.length; i++) {
                        slides[i].style.display = "none";
                    }
                    slides[slideIndex-1].style.display = "block";
                }
            </script>

            <title>Asignación de Pedidos</title>
        </head>
        <body>
            <main>
                <input type="checkbox" id="btn-modal">
                <label for="btn-modal" id="btn-cerrar">X</label>
                <div class="contenedor-modal">
                    <div class="contenido-modal">
                        <h2>SOPORTE DE ENVÍO # {$numSeguimiento}</h2>
HTML;
//Agregar codigo para imagenes de evidencia modo modal
    //Una sola imagen de evidencia - soporte
    $htmlModal="";
    $sizeUrlSoporte = sizeof($urlSoporte);
    $parameters = getParametros();
    if (is_array($urlSoporte)){
        if ($sizeUrlSoporte == 1){ 
            $htmlModal = checkFileExtension($urlSoporte[0], true, 0, 0);
        }else{
            //Varias imagenes de evidencia - soporte
            $htmlModal = '<div class="contenedor-slides-modal">';
            $sizeUrlSoporte = sizeof($urlSoporte);
            for ($j=0; $j < $sizeUrlSoporte; $j++){
                if ($parameters['produccion_mode']) {       //En produccion

                } else {                                    //En desarrollo
                    $urlSoporte[$j] = $parameters['local_image_url'] . pathinfo($urlSoporte[$j])['basename'];
                }
                $numImagen = $j + 1;
                $htmlModal .= '<div class="slides fade">';
                $htmlModal .= checkFileExtension($urlSoporte[$j], false, $numImagen, $sizeUrlSoporte);
                $htmlModal .= '</div>';
            }
            $htmlModal .= <<<HTML
                    <!-- Botones adelante y atras -->
                    <a class="slideAnt" onclick="moverSlides(-1)">&#10094;</a> 
                    <a class="slideSig" onclick="moverSlides(1)">&#10095;</a>
                </div>
HTML;
        }
    }

//Continuación del codigo html
    $html2 = <<<HTML
                    </div>   
                    <label for="btn-modal" id="fondo-modal"></label>
                </div>
                <section id="banner">
                    <div>
                        <div id="mensaje-banner">SEGUIMIENTO DE ENVÍOS</div>
                        <img id="banner-normal" src="img/Banner_Header.jpg"/>
                        <img id="banner-responsive" src="img/Banner_Header_Responsive_Vertical.jpg"/>
                    </div>
                </section>
                <section id="sec-consulta">
                    <form action="ConsultaNumSeg.php" method="post" id="consulta-seguimiento">
                        <div id="con-num-seg-consulta">
                            <div class="text-h3" id="num-seg-label"><label >N° de entrega</label></div>
                            <div class="text-h3" id="num-seg-input"><input  type="text" name="numSeguimiento" placeholder="Buscar # de seguimiento" style="text-align: center;"></div>
                        </div>
                        <div id="con-botones-consulta">
                            <button class="g-recaptcha boton-consulta" 
                                data-sitekey="captcha_secret_key"
                                data-callback="onSubmit" 
                                data-size="invisible">
                                <img onmouseout="this.src='img/Boton_Consultar.png';" onmouseover="this.src='img/Boton_Consultar_HoverOver.png';" src="img/Boton_Consultar.png" />
                            </button>
                            <button class="boton-consulta" type="reset" name="Limpiar">
                                <img onmouseout="this.src='img/Boton_Limpiar.png';" onmouseover="this.src='img/Boton_Limpiar_HoverOver.png';" src="img/Boton_Limpiar.png" onclick="resetVisibility()"/>
                            </button>
                        </div>
                    </form>
                </section>
                <section id="sec-estado-envio" style="visibility: {$visibilidadSeccionFechas}; height: {$heightSeccionFechas};">
                    <div class="back-verde-deg" id="con-num-order">Resultados para {$numSeguimiento}</div>
                    <div id="contenedor-resultados">
                        <div id="con-fecha-entrega">
                            <div class="text-h3" id="lbl-fecha-entrega">FECHA ESTIMADA DE ENTREGA</div>
                            <div id="txt-fecha-entrega">{$fechasSeguimiento["fechaEstimadaEntrega"]}</div>
                        </div>
                        <div id="con-resultado-iconos">
                            <div id="con-int-resultado-iconos">
                                <div class="etapas" id="etapa-1">
                                    <div class="texto fecha-sup fecha-{$estadosEnvio[1]}">{$fechasSeguimiento["fechaProcesoLogístico"]}</div>
                                    <div class="icono"><img src="img/Icono_{$estadosEnvio[1]}_Estados_Envio_1.png" /></div> 
                                    <div class="texto texto-inf texto-{$estadosEnvio[1]}">En proceso<br>logístico</div>
                                </div>
                                <div class="etapas" id="etapa-2">
                                    <div class="texto texto-sup texto-{$estadosEnvio[2]}">Entrega a transportadora</div>
                                    <div class="icono"><img src="img/Icono_{$estadosEnvio[2]}_Estados_Envio_2.png" /></div> 
                                    <div class="texto fecha-inf fecha-{$estadosEnvio[2]}">{$fechasSeguimiento["fechaEntregaTransportadora"]}</div>
                                </div>
                                <div class="etapas" id="etapa-3">
                                    <div class="texto fecha-sup fecha-{$estadosEnvio[3]}">{$fechasSeguimiento["fechaEnTransito"]}</div>
                                    <div class="icono"><img src="img/Icono_{$estadosEnvio[3]}_Estados_Envio_3.png" /></div> 
                                    <div class="texto texto-inf texto-{$estadosEnvio[3]}">En tránsito<br>logístico</div>
                                </div>
                                <div class="etapas" id="etapa-4">
                                    <div id="sub-etapa-4">
                                    <div class="texto texto-sup texto-{$estadosEnvio[4]}" id="texto-mini-1">En reparto<br></div>
                                    <div class="texto texto-sup texto-{$estadosEnvio[4]}" id="texto-mini-2">(ciudad de destino)<br></div>
                                    </div>
                                    <div class="icono"><img src="img/Icono_{$estadosEnvio[4]}_Estados_Envio_4.png"/></div> 
                                    <div class="texto fecha-inf fecha-{$estadosEnvio[4]}">{$fechasSeguimiento["fechaEnReparto"]}</div>
                                </div>
                                <div class="etapas" id="etapa-5">
                                    <div class="texto fecha-sup fecha-{$estadosEnvio[5]}">{$fechasSeguimiento["fechaEntregado"]}</div>
                                    <div class="icono"><img src="img/Icono_{$estadosEnvio[5]}_Estados_Envio_5.png" /></div> 
                                    <div class="texto texto-inf texto-{$estadosEnvio[5]}">Entregado</div>
                                </div>
                                <div id="linea-seg">
                                    <img src="img/Linea_Seguimiento_Estados_Envio_{$fechaActiva}.png" />
                                </div>
                                <div id="linea-seg-responsive">
                                    <img src="img/Linea_Seguimiento_Responsive_Estados_Envio_{$fechaActiva}.jpg" />
                                </div>
                            </div>
                        </div>
HTML;

//Modificaciones acorde a dato de url soporte
        $soporte = <<<HTML
            <div id="con-resultado-soporte">
                <img src="img/Boton_off_Soporte.png" alt="sin imagen de soporte"/>
            </div>
HTML;
            if (!empty($urlSoporte)){
            $soporte = <<<HTML
                <div id="con-resultado-soporte">
                    <label for="btn-modal">
                        <img onmouseout="this.src='img/Boton_on_Soporte.png';" onmouseover="this.src='img/Boton_on_Soporte_HoverOver.png';" src="img/Boton_on_Soporte.png" onclick="actualSlide(1)"/>
                    </label>
                </div>
HTML;
        }

            //continua codigo HTML
    $html3 = <<<HTML
                    </div>
                </section>
            </main>
            <footer>
                <div class="back-verde-deg" id="footerdiv">
                    <p>&copy; Euclid Chemical Toxement todos los derechos reservados.</p>
                </div>
            </footer>
        </body>
HTML;
    return $html . $htmlModal . $html2 . $soporte . $html3;
    }

    function generarAlerta($mensaje){
        $alerta = '<script language="javascript">alert("' . $mensaje . '");</script>';
        echo $alerta;
    }

    function checkFileExtension($urlSoporte, $isAlone = true, $numImagen, $totalImagenes) {
        $parameters = getParametros();
        if (!$parameters['produccion_mode']) {                 //En DESARROLLO
            $urlSoporte = $parameters['local_image_url'] . pathinfo($urlSoporte)['basename'];
        }
        $extension = strtolower(pathinfo($urlSoporte)['extension']);
        $pdf_image = 'img/pdf.png';
        $tif_image = 'img/tif.png';
        $otro_image = 'img/otro.png';
        $html_answer = '<a href="'.$urlSoporte.'" target="_blank">';
        if ($extension == 'pdf') {                            //PDF
            $html_answer .= '<img class="icono-modal" src="'.$pdf_image.'" ';
        }
        if ($extension == 'tif' || $extension == 'tiff') {    //TIF o TIFF
            $html_answer .= '<img class="icono-modal" src="'.$tif_image.'" ';
        }
        //                                                    ES IMAGEN
        if ($extension == 'jpg' || $extension == 'jpeg' || $extension == 'bmp' || $extension == 'png' || $extension == 'gif') {
            $html_answer .= '<img class="img-modal" src="'.$urlSoporte.'" ';
        } else {                                        //NINGUNO DE LOS ANTERIORES
            $html_answer .= '<img class="icono-modal" src="'.$otro_image.'" ';
        }
        if ($isAlone == true) {
            $html_answer .= 'alt="imagen de soporte del envío"/></a>';
        } else {
            $html_answer .= 'alt="imagen '.$numImagen.' de soporte del envío"/></a>';
            $html_answer .= '<div class="text">EVIDENCIA: '.$numImagen.' / '.$totalImagenes.'</div>';
        }
        return $html_answer;

    }


?>
