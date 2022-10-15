<?

// Základní informace o táboře (očesáno jen na ty, které potřebuje generátor)
$informace = array(
	"Datum" =>          new DateTime('2022-07-03') 	// PHP DateTime začátku
);

// Seznam skupinek a jejich barev
$skupinky = array (
	1 => array(
		"Nazev" =>	'Modří',
		"Barva" =>	"#0070C0"
	),
	2 => array(
		"Nazev" =>	'Zelení',
		"Barva" =>	"#00B050"
	),
	3 => array(
		"Nazev" =>	'Žlutí',
		"Barva" =>	"#FFFF00"
	),
	4 => array(
		"Nazev" =>	'Červení',
		"Barva" =>	"#FF0000"
	),
);

// Seznam dětí (ideální je tohle číst z databáze)
$deti = array (
	array (
		"Id"			=> 1,															// Unikátní identifikátor
		"Jmeno"			=> 'Josef',														// Křestní jméno
		"Prijmeni"		=> 'Poulíček',													// Příjmení
		"Prezdivka"		=> 'Bizon',														// Přezdívka
		"Narozeni"		=> new DateTime('2000-01-01'),									// Datum narození															
		"Skupinka"		=> 1, 															// ID skupinky
		"Pohlavi"		=> 'm', 														// 'm' nebo 'z'
		"Plavec"		=> 1,															// 1 nebo 0 pokud je či není plavec
		"Zdravotni"		=> 'Trpí vážnou demencí po tom, co se na táboře sekl do hlavy', // Zdravotní či jiná sdělení
		"Kontakt"		=> 'Anna Poulíčková (Matka)',									// Kontaktní osoba
		"Telefon"		=> '123 456 789',												// Telefon na kontaktní osobu
		"Pojistovna"	=> 'VZP',														// Pojišťovna
		"Stan"			=> 1															// Číslo stanu
	),
	array (
		"Id"			=> 2,
		"Jmeno"			=> 'Vendula',
		"Prijmeni"		=> 'Kotajná',
		"Prezdivka"		=> 'Vendy',
		"Narozeni"		=> new DateTime('2002-01-01'),
		"Skupinka"		=> 2,
		"Pohlavi"		=> 'z',
		"Plavec"		=> 0,
		"Zdravotni"		=> 'Je prostě příliš krásná na dětské tábory',
		"Kontakt"		=> 'Josef Poulíček (Otec)',
		"Telefon"		=> '123 456 789',
		"Pojistovna"	=> 'VZP',
		"Stan"			=> 2
	)
	// Sem přidávej další děti
)

?>
