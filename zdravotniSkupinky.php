<?php
require_once 'lib.php';

$css = '<style>'.file_get_contents(__DIR__.'/style.css').'</style>';

$html = $css;
$html .= seznamZdravotniSkupinky($deti, $skupinky, $informace['Datum']);

renderResult($html);
?>