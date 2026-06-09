<?php

declare(strict_types=1);

/*
==================================================
    PROCVIČOVÁNÍ PHP – LEKCE 2 (pokročilejší)
    Pole, řetězce, podmínky, cykly, kombinace
==================================================

Instrukce:
- Každý úkol řešte úpravou příslušné funkce.
- Neměňte názvy funkcí ani parametry.
- U každé funkce vytvořte alespoň 2 testovací volání dole.
- Nepoužívejte zbytečně hotové vestavěné funkce,
  pokud je cílem procvičit cykly a podmínky.
- Pište přehledný a odsazený kód.
*/


/*
--------------------------------------------------
1) Průměr známek
--------------------------------------------------
Pole obsahuje známky (1–5).
Vraťte průměr známek jako float.

Pokud je pole prázdné, vraťte 0.
*/
function prumerZnamek(array $znamky): float
{
	// TODO: Doplňte řešení
}


/*
--------------------------------------------------
2) Najdi nejdelší slovo
--------------------------------------------------
Pole obsahuje řetězce.
Vraťte nejdelší slovo.

Pokud je pole prázdné, vraťte prázdný řetězec.
*/
function nejdelsiSlovo(array $slova): string
{
	// TODO: Doplňte řešení
}


/*
--------------------------------------------------
3) Počet výskytů znaku v textu
--------------------------------------------------
Spočítejte, kolikrát se v textu nachází daný znak.
Nepoužívejte substr_count().
*/
function pocetVyskytu(string $text, string $znak): int
{
	// TODO: Doplňte řešení
}


/*
--------------------------------------------------
4) Je heslo dostatečně silné?
--------------------------------------------------
Heslo je silné, pokud:
- má alespoň 8 znaků
- obsahuje alespoň jedno číslo
- obsahuje alespoň jedno velké písmeno

Vraťte true / false.
*/
function jeSilneHeslo(string $heslo): bool
{
	// TODO: Doplňte řešení
}


/*
--------------------------------------------------
5) Součet pouze kladných čísel
--------------------------------------------------
Pole obsahuje různá čísla (kladná i záporná).
Vraťte součet pouze kladných čísel.
*/
function soucetKladnych(array $cisla): int
{
	// TODO: Doplňte řešení
}


/*
--------------------------------------------------
6) Převod teplot
--------------------------------------------------
Pokud je jednotka:
"C" → převeď na Fahrenheit
"F" → převeď na Celsius

Vzorce:
C → F: (C * 9/5) + 32
F → C: (F - 32) * 5/9

Pokud je jiná jednotka, vraťte null.
*/
function prevedTeplotu(float $hodnota, string $jednotka): ?float
{
	// TODO: Doplňte řešení
}


/*
--------------------------------------------------
7) Jednoduchá statistika pole
--------------------------------------------------
Vraťte asociativní pole ve tvaru:

[
    "min" => nejmenší hodnota,
    "max" => největší hodnota,
    "pocet" => počet prvků
]

Nepoužívejte min() ani max().
Pokud je pole prázdné, vraťte prázdné pole.
*/
function statistikaPole(array $cisla): array
{
	// TODO: Doplňte řešení
}


/*
--------------------------------------------------
8) Odstranění duplicit
--------------------------------------------------
Vraťte nové pole bez duplicit.
Nepoužívejte array_unique().
*/
function odstranDuplicity(array $pole): array
{
	// TODO: Doplňte řešení
}


/*
--------------------------------------------------
9) Jednoduchý nákupní košík
--------------------------------------------------
Pole obsahuje ceny produktů.
Vraťte celkovou cenu.

Pokud je celková cena vyšší než 1000,
poskytněte slevu 10 %.

Vrací výslednou cenu.
*/
function spocitejKosik(array $ceny): float
{
	// TODO: Doplňte řešení
}


/*
--------------------------------------------------
10) Šachovnicový výpis
--------------------------------------------------
Vypište čtverec o velikosti $n
z hvězdiček a mezer střídavě.

Např. pro 4:

* * *
 * * *
* * *
 * * *

Použijte vnořené cykly.
*/
function sachovnice(int $n): void
{
	// TODO: Doplňte řešení
}


/*
--------------------------------------------------
BONUS
--------------------------------------------------
Vytvořte funkci, která zjistí,
zda je text palindrom (čte se stejně
zepředu i zezadu).

Nepoužívejte strrev().
Ignorujte velikost písmen.
*/
function jePalindrom(string $text): bool
{
	// TODO: Doplňte řešení
}


/*
==================================================
TESTOVACÍ VOLÁNÍ
(Odkomentujte po dokončení funkcí)
==================================================

// var_dump(prumerZnamek([1,2,3,4]));
// var_dump(prumerZnamek([]));

// var_dump(nejdelsiSlovo(["pes", "kočka", "slon"]));

// var_dump(pocetVyskytu("programovani", "o"));

// var_dump(jeSilneHeslo("Test1234"));
// var_dump(jeSilneHeslo("abc"));

// var_dump(soucetKladnych([-5, 10, -3, 7]));

// var_dump(prevedTeplotu(0, "C"));
// var_dump(prevedTeplotu(32, "F"));

// var_dump(statistikaPole([5,2,9,1]));

// var_dump(odstranDuplicity([1,2,2,3,1,4]));

// var_dump(spocitejKosik([200, 500, 400]));

// sachovnice(4);

// var_dump(jePalindrom("Kajak"));
// var_dump(jePalindrom("PHP"));

*/