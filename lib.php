<?php
require_once '__data.php';
require_once 'dompdf/autoload.inc.php';
use Dompdf\Dompdf;

function renderResult($html) {
    $dompdf = new Dompdf();

    if (isset($_GET['html'])) {
        echo '<html><head><meta charset="utf-8"></head><body>'.$html.'</body></html>';
    } else {
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        $dompdf->stream("dompdf_out.pdf", array("Attachment" => false));
        exit(0);
    }
}

function opravPrezdivky($people) {
    $prezdivky = array();

    foreach ($people as $key => $value) {
        array_push($prezdivky, $value['Prezdivka'] == '' ? $value['Jmeno'] : $value['Prezdivka']);
        if ($value['Prezdivka'] == '') {
            $people[$key]['Prezdivka'] = $value['Jmeno'];
        }
    }

    foreach ($people as $key => $value) {
        foreach ($prezdivky as $key2 => $prezdivka) {
            if (strcasecmp($value['Prezdivka'], $prezdivka) == 0 && $key != $key2) {
                $people[$key]['Prezdivka'] = $people[$key]['Prezdivka'].' '.mb_substr($value['Prijmeni'], 0, 1).'.';
                break;
            }
        }
    }
    return $people;
}

function removeDiacritics($text) {
    // pridano 'z' kvuli korektnimu razeni
    $prevodni_tabulka = Array('á'=>'az', 'Á'=>'Az', 'č'=>'cz', 'Č'=>'Cz', 'ď'=>'dz', 'Ď'=>'Dz', 'ě'=>'ez', 'Ě'=>'Ez', 'é'=>'ez', 'É'=>'Ez', 'í'=>'iz', 'Í'=>'Iz', 'ň'=>'nz', 'Ň'=>'Nz', 'ó'=>'oz', 'Ó'=>'Oz', 'ř'=>'rz', 'Ř'=>'Rz', 'š'=>'sz', 'Š'=>'Sz', 'ť'=>'tz', 'Ť'=>'Tz', 'ú'=>'uz', 'Ú'=>'Uz', 'ů'=>'uz', 'Ů'=>'Uz', 'ý'=>'yz', 'Ý'=>'Yz', 'ž'=>'zz', 'Ž'=>'Zz');
    return strtr($text, $prevodni_tabulka);
}

function comparatorAbeceda($object1, $object2) {
    return strcmp(removeDiacritics($object1['Prezdivka']), removeDiacritics($object2['Prezdivka']));
} 

function comparatorPrijmeni($object1, $object2) {
    return strcmp(removeDiacritics($object1['Prijmeni']), removeDiacritics($object2['Prijmeni']));
} 

function comparatorVek($object1, $object2) {
    return $object1['Narozeni'] > $object2['Narozeni'];
} 

function comparatorVek2($object1, $object2) {
    return $object1['Narozeni'] < $object2['Narozeni'];
} 

function comparatorStany($object1, $object2) {
    if ($object1['Stan'] != $object2['Stan']) {
        return $object1['Stan'] > $object2['Stan'];
    }
    return strcmp($object1['Prezdivka'], $object2['Prezdivka']);
} 

function formatDay($dateTime) {
    $day = '';
    switch ($dateTime->format('N')) {
        case "1": $day = "PO"; break;
        case "2": $day = "ÚT"; break;
        case "3": $day = "ST"; break;
        case "4": $day = "ČT"; break;
        case "5": $day = "PÁ"; break;
        case "6": $day = "SO"; break;
        case "7": $day = "NE"; break;
    }
    return '<span class="day">'.$day.'</span><span class="date">'.$dateTime->format('d/m').'</span>';
}

function getRoommates($people, $groups, $stan, $excludeId) {
    $roommate = '';
    foreach ($people as $other) {
        if ($other['Stan'] === $stan && $other['Id'] !== $excludeId) {
            if (strlen($roommate) > 0) $roommate .= ' a ';
            $roommate .= /*'<span style="color: '.$groups[$other['Skupinka']]['Barva'].'">#</span> '.*/$other['Prezdivka'];
        }
    }
    return $roommate;
}

function seznamAbeceda($title, $people, $groups, $cols) {
    usort($people, 'comparatorAbeceda');
    
    $result = '';
    $result .= '<span class="page"><table><tr class="header"><td colspan="2">'.$title.'</td>';
    for ($j = 0; $j < $cols; $j++) {
        $result .= '<td></td>';
    }
    $result .= '</tr>';
    
    $i = 1;
    foreach ($people as $value) {
        $color = isset($groups[$value['Skupinka']]) ? $groups[$value['Skupinka']]['Barva'] : "white";
        $result .= '<tr><td class="number" style="background-color: '.$color.'">'.$i.'</td><td class="name">'.$value['Prezdivka'].'</td>';
        for ($j = 0; $j < $cols; $j++) {
            $result .= '<td></td>';
        }
        $result .= '</tr>';
        $i++;
    }

    $result .= '</table></span>';
    return $result;
}

function seznamSkupinky($title, $people, $groups, $cols) {
    usort($people, 'comparatorVek');

    $result = '';
    $result .= '<span class="page"><table><tr class="header"><td colspan="2">'.$title.'</td>';
    for ($j = 0; $j < $cols; $j++) {
        $result .= '<td></td>';
    }
    $result .= '</tr>';

    $others = '';
    $othersCount = 0;

    foreach ($people as $value) {
        if (isset($groups[$value['Skupinka']])) {
            if (isset($groups[$value['Skupinka']]['Content'])) {
                $groups[$value['Skupinka']]['Content'] .= '<tr>';
            } else {
                $groups[$value['Skupinka']]['Content'] = '';
                $groups[$value['Skupinka']]['Count'] = 0;
            }
            $groups[$value['Skupinka']]['Content'] .= '<td class="name">'.$value['Prezdivka'].'</td>';
            for ($j = 0; $j < $cols; $j++) {
                $groups[$value['Skupinka']]['Content'] .= '<td></td>';
            }
            $groups[$value['Skupinka']]['Content'] .= '</tr>';
            $groups[$value['Skupinka']]['Count']++;
        } else {
            $othersCount++;
            if ($others != '') {
                $others .= '<tr>';
            }
            $others .= '<td class="name">'.$value['Prezdivka'].'</td>';
            for ($j = 0; $j < $cols; $j++) {
                $others .= '<td></td>';
            }
            $others .= '</tr>';
        }
    }

    foreach ($groups as $value) {
        if (isset($value['Count'])) {
            $result .= '<tr class="first"><td class="group" style="background-color: '.$value['Barva'].'" rowspan="'.$value['Count'].'"><span>'.$value['Nazev'].'</span></td>'.$value['Content'];
        }
    }
    if ($othersCount > 0) {
        $result .= '<tr class="first"><td class="group" style="background-color: white" rowspan="'.$othersCount.'"><span></span></td>'.$others;
    }

    $result .= '</table></span>';
    return $result;
}

function seznamSprchy($title, $people, $groups, $cols, $start) {
    usort($people, 'comparatorVek2');
    
    $result = '';
    $result .= '<span class="page">';
    
    $cur = new DateTime($start->format('Y-m-d H:i:s'));

    $kluci = '<table>';
    $holky = '<table><tr class="header small"><td colspan="2">'.$title.'</td>';
    for ($j = 0; $j < $cols; $j++) {
        $holky .= '<td>'.formatDay($cur).'</td>';
        $cur->add(new DateInterval('P1D'));
    }
    $holky .= '</tr>';
    
    $i = 1;
    foreach ($people as $value) {
        if ($value['Pohlavi'] == 'm') {
            $kluci .= '<tr><td class="name">'.$value['Prezdivka'].'</td><td class="age">'.($value['Narozeni']->diff($start)->y).'</td>';
            for ($j = 0; $j < $cols; $j++) {
                $kluci .= '<td></td>';
            }
            $kluci .= '</tr>';
        } else {
            $holky .= '<tr><td class="name">'.$value['Prezdivka'].'</td><td class="age">'.($value['Narozeni']->diff($start)->y).'</td>';
            for ($j = 0; $j < $cols; $j++) {
                $holky .= '<td></td>';
            }
            $holky .= '</tr>';
        }
        $i++;
    }

    $kluci .= '</table>';
    $holky .= '</table>';
    $result .= $holky.$kluci.'</span>';
    return $result;
}

function seznamStany($title, $people, $groups, $cols, $start, $showAge, $offset) {
    usort($people, 'comparatorStany');
    
    $cur = new DateTime($start->format('Y-m-d H:i:s'));

    $result = '';
    $result .= '<span class="page"><table><tr class="header small"><td colspan="'.($showAge ? 3 : 2).'">'.$title.'</td>';
    for ($j = 0; $j < $cols; $j++) {
        $result .= '<td>'.formatDay($cur).'</td>';
        $cur->add(new DateInterval('P1D'));
    }
    $result .= '</tr>';
    
    $current = '';
    $currentNumber = 0;
    $currentPeople = 0;
    $currentId = 0;

    foreach ($people as $value) {
        if ($value['Stan'] != $currentNumber) {
            if ($currentNumber != 0) {
                $result .= '<tr class="first"><td class="number" rowspan="'.$currentPeople.'">'.($currentId + $offset).'</td>'.$current;
            }
            $current = '<td class="name">'.$value['Prezdivka'].'</td>';
            if ($showAge) {
                $color = isset($groups[$value['Skupinka']]) ? $groups[$value['Skupinka']]['Barva'] : "white";
                $current .= '<td class="age" style="background-color: '.$color.'">'.($value['Narozeni']->diff($start)->y).'</td>';
            }
            $currentNumber = $value['Stan'];
            $currentPeople = 1;
            $currentId ++;
        } else {
            $current .= '<tr><td class="name">'.$value['Prezdivka'].'</td>';
            if ($showAge) {
                $color = isset($groups[$value['Skupinka']]) ? $groups[$value['Skupinka']]['Barva'] : "white";
                $current .= '<td class="age" style="background-color: '.$color.'">'.($value['Narozeni']->diff($start)->y).'</td>';
            }
            $currentPeople++;
        }

        for ($j = 0; $j < $cols; $j++) {
            $current .= '<td></td>';
        }
        $current .= '</tr>';
    }

    if ($currentNumber != 0) {
        $result .= '<tr class="first"><td class="number" rowspan="'.$currentPeople.'">'.($currentId + $offset).'</td>'.$current;
    }

    $result .= '</table></span>';
    return $result;
}

function seznamKruh($people, $groups, $start) {
    usort($people, 'comparatorAbeceda');
    
    $result = '';
    $result .= '<div class="circular-container">';
    
    $step = 360 / (count($people));
    $r = 0;
    foreach ($people as $value) {
        $color = $value['Pohlavi'] == 'm' ? "#0070C0" : "#FF0000";
        $result .= '<div class="circular-cell" style="transform: rotate('.$r.'deg)"><div class="circular-border">'.$value['Prezdivka'].' ('.$value['Narozeni']->diff($start)->y.')</div><div style="background-color: '.$color.'" class="circular-mark">&nbsp;</div></div>';
        $r += $step;
    }

    $result .= '</div>';
    return $result;
}

function seznamSkupinkyAbeceda($title, $people, $groups, $cols) {
    usort($people, 'comparatorPrijmeni');

    $result = '';
    $result .= '<span class="page"><table><tr class="header"><td colspan="2">'.$title.'</td>';
    for ($j = 0; $j < $cols; $j++) {
        $result .= '<td></td>';
    }
    $result .= '</tr>';

    foreach ($people as $value) {
        if (isset($groups[$value['Skupinka']])) {
            if (isset($groups[$value['Skupinka']]['Content'])) {
                $groups[$value['Skupinka']]['Content'] .= '<tr>';
            } else {
                $groups[$value['Skupinka']]['Content'] = '';
                $groups[$value['Skupinka']]['Count'] = 0;
            }
            $groups[$value['Skupinka']]['Content'] .= '<td class="name">'.$value['Prijmeni'].' '.substr($value['Jmeno'], 0, 1).'.</td>';
            for ($j = 0; $j < $cols; $j++) {
                $groups[$value['Skupinka']]['Content'] .= '<td></td>';
            }
            $groups[$value['Skupinka']]['Content'] .= '</tr>';
            $groups[$value['Skupinka']]['Count']++;
        }
    }

    foreach ($groups as $value) {
        if (isset($value['Count'])) {
            $result .= '<tr class="first"><td class="group" style="background-color: '.$value['Barva'].'" rowspan="'.$value['Count'].'"><span>'.$value['Nazev'].'</span></td>'.$value['Content'];
        }
    }

    $result .= '</table></span>';
    return $result;
}

function seznamZdravotniAbeceda($title, $people, $groups, $start) {
    usort($people, 'comparatorPrijmeni');
    
    $result = '';
    $result .= '<span class="page"><table><tr class="header small"><td colspan="2">'.$title.'</td>';
    $result .= '<td></td>';
    $result .= '</tr>';
    
    $perpage = 10;

    $i = 0;
    foreach ($people as $value) {
        $zdravotni = '<div style="padding-bottom: 2px;"><i>Kontakt: </i>'.$value['Kontakt'].' (tel. '.$value['Telefon'].')</div>';
        $zdravotni .= '<div style="padding-bottom: 2px;"><i>Pojišťovna: </i>'.$value['Pojistovna'].'</div>';
        $zdravotni .= ($value['Plavec'] == 0 ? '<span style="color:blue">neplavec</span>'.(strlen($value['Zdravotni']) > 0 ? ', ' : '') : '');
        $zdravotni .= '<span style="color:red">'.$value['Zdravotni'].'</span>';

        $color = isset($groups[$value['Skupinka']]) ? $groups[$value['Skupinka']]['Barva'] : "white";

        $roommate = getRoommates($people, $groups, $value['Stan'], $value['Id']);

        $result .= '<tr><td class="number zdravotni" style="background-color: '.$color.'">'.($i + 1).'</td><td class="name-full zdravotni"><b>'.$value['Prijmeni'].' '.$value['Jmeno'].' </b><br/>
                    '.$value['Prezdivka'].' (<i>'.($value['Narozeni']->diff($start)->y).' let</i>)<br/><br/>
                    '.($value['Stan'] > 0 ? ('<i>Stan '.$value['Stan'].(strlen($roommate) > 0 ? ' ('.$roommate.')' : '').'</i>') : '').'
                    </td>';
        $result .= '<td class="zdravotni">'.$zdravotni.'</td>';
        $result .= '</tr>';
        $i++;
        if ($i % $perpage == 0) {
            $result .= '</table><div class="pagenum">'.(ceil($i / $perpage)).'/'.ceil(count($people) / $perpage).'</div></span><span class="page"><table><tr class="header small"><td colspan="2">'.$title.'</td>';
            $result .= '<td></td>';
            $result .= '</tr>';
        }
    }

    $result .= '</table><div class="pagenum">'.(ceil($i / $perpage)).'/'.ceil(count($people) / $perpage).'</div></span>';
    return $result;
}

function seznamZdravotniSkupinky($people, $groups, $start) {
    usort($people, 'comparatorPrijmeni');

    $result = '';

    foreach(array_keys($groups) as $skupinka) {
        $result .= '<span class="page"><table><tr class="header small" style="background-color: '.$groups[$skupinka]['Barva'].'"><td colspan="3">'.$groups[$skupinka]['Nazev'].'</td>';
        $result .= '</tr>';

        $i = 0;
        foreach ($people as $value) {
            if ($value['Skupinka'] == $skupinka) {
                $roommate = getRoommates($people, $groups, $value['Stan'], $value['Id']);

                $result .= '<tr><td class="number zdravotni small">'.($i + 1).'</td><td class="name-full zdravotni small"><b>'.$value['Prijmeni'].' '.$value['Jmeno'].' </b><br/>
                            '.$value['Prezdivka'].' (<i>'.($value['Narozeni']->diff($start)->y).' let</i>)<br/>
                            '.($value['Stan'] > 0 ? ('<i>stan '.$value['Stan'].(strlen($roommate) > 0 ? ' ('.$roommate.')' : '').'</i>') : '&nbsp;').'
                            </td>';
                $result .= '<td class="zdravotni small">'.($value['Plavec'] == 0 ? '<span style="color:blue">neplavec</span>'.(strlen($value['Zdravotni']) > 0 ? ', ' : '') : '').$value['Zdravotni'].'</td>';
                $result .= '</tr>';
                $i++;
            }
        }

        $result .= '</table></span>';
    }
    return $result;
}

function seznamZdravotniText($people, $groups, $start) {
    usort($people, 'comparatorVek');

    $result = '';

    foreach(array_keys($groups) as $skupinka) {
        $result .= '<h3>'.$groups[$skupinka]['Nazev'].'</h3>';
        $result .= '<table>';

        $i = 0;
        foreach ($people as $value) {
            if ($value['Skupinka'] == $skupinka) {
                $roommate = getRoommates($people, $groups, $value['Stan'], $value['Id']);

                $result .= '<tr>';
                $result .= '<td>'.$value['Prezdivka']." <span style='opacity: 0.4'>(".$value['Narozeni']->diff($start)->y.")</span>".'</td>';
                $result .= '<td><span style="opacity: 0.4">'."stan ".$value['Stan'].(strlen($roommate) > 0 ? ' ('.$roommate.')' : '').'</span></td>';
                $result .= '<td><span style="opacity: 0.4">'.($value['Plavec'] == 0 ? "Neplavec":"").'</span></td>';
                $result .= '<td><span style="opacity: 0.4">'.$value['Zdravotni'].'</span></td>';
                $result .= '</tr>';
                $i++;
            }
        }

        $result .= '</table><br/>';
    }
    return $result;
}

function panacci($people) {
    usort($people, 'comparatorPrijmeni');

    $data = file_get_contents(__DIR__.'/panacek.png');
    $base64 = 'data:image/png;base64,' . base64_encode($data);

    $result = '';

    $i = 0;

    $result .= '<span class="page"><div class="clear-pagetop">&nbsp;</div>';

    foreach ($people as $value) {
        if ($i % 5 == 2 || $i % 5 == 3) {
            $result .= '<br/>';
        }
        $result .= '<div class="panacek '.($i % 5 == 2 ? "flipped" : "").'"><img src="'.$base64.'" class="panacek-img"/><div class="panacek-jmeno">'.$value['Jmeno'].' '.$value['Prijmeni'].'</div></div>';
        $i++;
        if ($i % 5 == 0) {
            $result .= '</span><span class="page"><div class="clear-pagetop">&nbsp;</div>';
        }
    }

    $result .= '</span>';

    return $result;
}

?>