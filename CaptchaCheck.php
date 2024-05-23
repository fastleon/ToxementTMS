<?php
function verificarCaptcha(){
    $isCaptchaOk = FALSE;
    if (isset($_POST['g-recaptcha-response'])) {
        //------------------------------------------
        $secret = 'captcha_secret_key';

        // Captura la respuesta del reCAPTCHA
        $recaptcha_response = $_POST['g-recaptcha-response'];

        // URL del endpoint de verificación de reCAPTCHA
        $verify_url = 'https://www.google.com/recaptcha/api/siteverify';

        // Parámetros que se enviarán en la solicitud POST
        $data = array(
            'secret' => $secret,
            'response' => $recaptcha_response
        );

        $options = array(
            'http' => array(
                'header' => "Content-type: application/x-www-form-urlencoded\r\n",
                'method' => 'POST',
                'content' => http_build_query($data)
            )
        );

        $context  = stream_context_create($options);
        $verify_response = file_get_contents($verify_url, false, $context);

        // Procesa la respuesta del servidor
        $verify_response = json_decode($verify_response);
        //var_dump($verify_response);

        // Verifica si la solicitud fue exitosa
        if ($verify_response->success) {
            $isCaptchaOk = TRUE;
        }
    }
    return $isCaptchaOk;
}
?>