<?php
/*
*$numSeguimiento -> si esta vacio ("") no se mostrara la seccion de fechas y sus iconos
*$fechasSeguimiento -> array de 6 fechas en el siguiente orden:
*   (fecha) fecha estimada de entrega : fechaEstimadaEntrega
*   (fecha) En proceso logístico : fechaProcesoLogístico
*   (fecha) Entrega a transportadora : fechaEntregaTransportadora
*   (fecha) En transito : fechaEnTransito
*   (fecha) En reparto : fechaEnReparto
*   (fecha) Entregado : fechaEntregado
*$urlSoporte -> url a donde dirige el boton de Soporte
*
*EL BOTON CONSULTA LLAMA AL ARCHIVO ConsultaNumSeg.php 
*/
include "Seguimiento.php";
    $numSeguimiento = ""; //"345345345";
    $fechasSeguimiento = array("fechaEstimadaEntrega" => "10/10/2000",
            "fechaProcesoLogístico" => "01/01/2001",
            "fechaEntregaTransportadora" => "02/02/2002",
            "fechaEnTransito" => "03/03/2003",
            "fechaEnReparto" => "04/04/2004",
            "fechaEntregado" => "05/05/2005");
    $urlSoporte = "";
    $html = generarHTML($numSeguimiento, $fechasSeguimiento, $urlSoporte);
    echo $html;
?>