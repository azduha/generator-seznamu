<?php

require_once 'lib.php';

$css = '<style>'.file_get_contents(__DIR__.'/style.css').'</style>';

$html = '';
$html .= seznamSprchy($css, 'SPRCHOVÁNÍ', $deti, $skupinky, 12, $informace['Datum']);
$html .= seznamStany($css, 'SLUŽBY, HLÍDKY', $deti, $skupinky, 12, $informace['Datum'], true, 13);
$html .= seznamStany($css, 'ÚKLID', $deti, $skupinky, 12, $informace['Datum'], false, 13);

renderResult($html);

?>