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
*/

  //Informacion
  $fechaEstimadaEntrega = "";
  $fechaProcesoLogistico = "";
  $fechaEntregaTransportadora = "";
  $fechaEnTransito = "";
  $fechaEnReparto = "";
  $fechaEntregado = "";
  $numSeguimiento = "";
  $urlSoporte = "";

  include "Seguimiento.php";
  include "CaptchaCheck.php";

  if (isset($_POST["numSeguimiento"])){
    if ($_POST["numSeguimiento"] == ""){
      //No digitaron ningun valor en el espacio
      generarAlerta("Debe digitar un valor en numero de seguimiento");
    }else if(!verificarCaptcha()){
      //verificacion de autenticación captcha
      generarAlerta ("Fallo de autenticación captcha");
    }else{
      //consultar dato en base de datos
      $base_url = 'http://10.10.10.6:8080/ToxementIntranetRestServices/TMS';
	    //$base_url = 'http://127.0.0.1:38080/ToxementIntranetRestServices/TMS';
      $base_image_url ='https://app.rcontrol.com.mx/Pictures/';
	  
      $service_name = '/consultarEmbarque';
      $shipment_value = $_POST["numSeguimiento"];
      
      // Construct the complete URL with the shipment parameter
      $request_url = $base_url . $service_name . '?shipment=' . urlencode($shipment_value);

      // Make the GET request
      try{
        $response = file_get_contents($request_url);
      } catch (Exception $e){
        generarAlerta ("No es posible tener conexión con el servidor, verifique su conexión e intente de nuevo.");
        $response = "";
      }

      $response = json_decode($response);

      if (property_exists($response, 'activity_num')){        //Se recibieron datos
        if ($response->activity_num != ""){
          // Verificando que existen las propiedades y contienen datos
          if (property_exists($response, 'fechaPlanificadaEntrega')) {
            if (!is_null($response->fechaPlanificadaEntrega)){
              $fechaEstimadaEntrega = $response->fechaPlanificadaEntrega;
            }
          }
          // Revision de las URLs de SOPORTE 
          if (property_exists($response, 'urlSoporte')) {
			      $urlSoporte = array();
            if ( !empty($response->urlSoporte) ){
              $urlSoportes = $response->urlSoporte;
			        $i=0;
              foreach($urlSoportes as $url){
				        $urlSoporte[$i] = $base_image_url . $url;
				        $i++;
              }
            }
          }
          if (property_exists($response, 'tracks')){
            if (!empty($response->tracks)){
              $eventos = $response->tracks;
              $numSeguimiento = $shipment_value;
              foreach($eventos as $evento){
                $descripcionEvento = $evento->evento;
                if ($descripcionEvento == "Entregado"){
                  $fechaEntregado = $evento->fechaFormato;
                }else if($descripcionEvento == "Reparto"){
                  $fechaEnReparto = $evento->fechaFormato;
                }else if($descripcionEvento == "En tránsito"){
                  $fechaEnTransito = $evento->fechaFormato;
                }else if($descripcionEvento == "Entrega a transportadora"){
                  $fechaEntregaTransportadora = $evento->fechaFormato;
                }else if($descripcionEvento == "En proceso logístico"){
                  $fechaProcesoLogistico = $evento->fechaFormato;
                }
              }
            }
          }
        }else{
          //echo "NO SE ENCONTRARON DATOS DE ESE NUMERO DE SEGUIMIENTO";
          generarAlerta("No se encontró información para ese número de seguimiento.");
        }
      }else{
        //echo "No hay respuesta por parte del servidor.";
        generarAlerta("No hay respuesta por parte del servidor.");
      }
    }
  }

  $fechasSeguimiento = array("fechaEstimadaEntrega" => $fechaEstimadaEntrega,
          "fechaProcesoLogístico" => $fechaProcesoLogistico,
          "fechaEntregaTransportadora" =>$fechaEntregaTransportadora,
          "fechaEnTransito" => $fechaEnTransito,
          "fechaEnReparto" => $fechaEnReparto,
          "fechaEntregado" => $fechaEntregado);
          
  $html = generarHTML($numSeguimiento, $fechasSeguimiento, $urlSoporte);
  echo $html;
?>