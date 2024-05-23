<?php

function getParametros() {
    return array(
        'produccion_mode' => true,
        'base_url' => '',  //URL de la pagina en servidor local
        'base_image_url' => '', //'https://www.servidor.ejemplo.com/Pictures/', Servidor donde se se alamacenan las imagenes de soporte
        'local_image_url' => '', // '/clientes/clientes/sites/default/files/clientestms/evidencias/',  //Ubicacion dem las imagenes para pruebas en Desarrollo
        'service_name' => '', // '/consltrEmbarque',    //query para realizar la consulta de los embarques
        'shipment_value' => 'numSeguimiento',    //parametro en app
    );
}
