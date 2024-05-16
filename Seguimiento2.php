<?php
echo "test";
$html2 = generarHTML();
echo $html2;

function generarHTML() {
    $html = <<< HTML
        hola
HTML;
    return $html;
}

?>  