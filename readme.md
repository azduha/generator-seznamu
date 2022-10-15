# Generátor seznamů na LDT Duha AZ

## Použití
V souboru [`__data.php`](__data.php) je třeba vyplnit informace o táboru a účastnících. Případně se dají data načítat do obnobného formátu z databáze.

## Instalace

K použití je třeba inicializovat git submodule `dompdf` a doinstalovat jeho závislosti

```bash
cd dompdf/lib

git clone https://github.com/PhenX/php-font-lib.git php-font-lib
cd php-font-lib
git checkout 0.5.1
cd ..

git clone https://github.com/PhenX/php-svg-lib.git php-svg-lib
cd php-svg-lib
git checkout v0.3.2
cd ..

git clone https://github.com/sabberworm/PHP-CSS-Parser.git php-css-parser
cd php-css-parser
git checkout 8.1.0
```