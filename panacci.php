<?php
require_once 'lib.php';

$css = '<style>'.file_get_contents(__DIR__.'/style.css').'</style>';

$html = $css;
$html .= panacci($deti);

renderResult($html);
?>