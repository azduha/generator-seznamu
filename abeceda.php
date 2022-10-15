<?php
require_once 'lib.php';

$css = '<style>'.file_get_contents(__DIR__.'/style.css').'</style>';

$html = $css;
$html .= seznamAbeceda('ABECEDA<br>ÚZKÝ', $deti, $skupinky, 13);
$html .= seznamAbeceda('ABECEDA<br>ŠIROKÝ', $deti, $skupinky, 7);
$html .= seznamAbeceda('ABECEDA<br>FULL', $deti, $skupinky, 1);
$html .= seznamSkupinky('SKUPINKY<br>ÚZKÝ', $deti, $skupinky, 13);
$html .= seznamSkupinky('SKUPINKY<br>ŠIROKÝ', $deti, $skupinky, 7);

renderResult($html);
?>