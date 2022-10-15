<?php

require_once 'lib.php';

$css = '<style>'.file_get_contents(__DIR__.'/style.css').'</style>';

$html = $css;
$html .= seznamSprchy('SPRCHOVÁNÍ', $deti, $skupinky, 12, $informace['Datum']->modify('+1 day'));
$html .= seznamStany('SLUŽBY, HLÍDKY', $deti, $skupinky, 12, $informace['Datum'], true, 0);
$html .= seznamStany('ÚKLID', $deti, $skupinky, 12, $informace['Datum'], false, 0);

renderResult($html);

?>