<?php

declare(strict_types=1);

/*
==================================================
    PROCVIČOVÁNÍ PHP – LEKCE 3 (pokročilejší)
    Matematika
==================================================

Instrukce:
· Nastavte si 3 strany do proměnných $a, $b, $c
· Zobrazte nastavené hodnoty na stránce (a = 10 cm, …)
· Určete, zda tento trojúhelník lze sestrojit, v případě, že nelze, vypíše se: trojúhelník nelze sestrojit a ukončí se vše
· Určete, zda tento trojúhelník je rovnostranný, rovnoramenný, nebo obecný a vypište.
· Vynechte dva řádky, provede se výpočet obvodu a obsahu (použijte Heronův vzorec 𝑆=√𝑠(𝑠−𝑎)(𝑠−𝑏)(𝑠−𝑐), kde 𝑠=𝑎+𝑏+𝑐2) podle zadání (odmocnina je sqrt($cislo)) a vypíše se: obvod (obsah) je hodnota
*/





/*
----------------------------------------------------
DALŠÍ ÚKOLY
----------------------------------------------------

5) Vytvořte funkci getTriangleAngleType($a, $b, $c),
   která určí typ trojúhelníku podle úhlů:

   - pravoúhlý
   - ostroúhlý
   - tupoúhlý

   Postup:
   Najděte nejdelší stranu (označte ji c).

   Porovnejte:

   c² ? a² + b²

	   c² = a² + b² → pravoúhlý
   c² < a² + b² → ostroúhlý
   c² > a² + b² → tupoúhlý

   Funkce vrátí text s typem trojúhelníku.

----------------------------------------------------

6) Vytvořte funkci getHeightToA($a, $content),
   která vypočítá výšku na stranu a.

Použijte vzorec:

   v_a = (2 * S) / a

   Funkce vrátí výšku.

----------------------------------------------------

7) Vytvořte funkci getAngles($a, $b, $c),
   která vypočítá velikosti úhlů α, β, γ.

Použijte kosinovou větu například pro α:

   cos α = (b² + c² − a²) / (2bc)

   Použijte funkce:
   acos()
   rad2deg()

   Výsledky zaokrouhlete na 2 desetinná místa.

Funkce vrátí pole:

   [
	   'alpha' => ...,
       'beta' => ...,
       'gamma' => ...
   ]

   ----------------------------------------------------

8) Vytvořte funkci getMinMaxSide($a, $b, $c),
   která vrátí nejdelší a nejkratší stranu.

Funkce vrátí pole:

   [
	   'min' => ...,
       'max' => ...
   ]

====================================================
FUNKCE – DOPLŇTE ŘEŠENÍ
====================================================
*/

function getTriangleAngleType(float $a, float $b, float $c): string
{
	// TODO: doplňte řešení
}


function getHeightToA(float $a, float $content): float
{
	// TODO: doplňte řešení
}


function getAngles(float $a, float $b, float $c): array
{
	// TODO: doplňte řešení
}


function getMinMaxSide(float $a, float $b, float $c): array
{
	// TODO: doplňte řešení
}

