---
title: Vyšel Lean Mapper 4
author: janpecha
---

{:.perex}
Lean Mapper 4 je tady. Pojďte se podívat co přináší.

Chystané novinky v Lean Mapperu 4 jsme si podrobně popisovali v [předchozím článku](/blog/2021-01-06-otestujte-leanmapper-4/). Dnes jen rychlý přehled.


## Podpora Dibi 4 a PHP 7.2+

Lean Mapper nyní vyžaduje Dibi 4 a PHP **7.2** nebo novější. Zároveň je plně kompatibilní s **PHP 8.0**.

Všechny metody a funkce nyní obsahují nativní typehinty, které přineslo PHP 7. Všechny soubory taky obsahují `declare(strict_types=1)`.


## Konverze hodnot z databáze

Lean Mapper nyní v mapperu umožňuje konvertovat hodnoty z databáze předtím než se předají do entity. Můžete tedy hodnoty převést třeba na value objekty nebo provádět jiné konverze.

Konverze se provádí v mapperu pomocí nových metod `convertToRowData` (databáze => entita) a `convertFromRowData` (entita => databáze).


## Vychozí namespace entit

Výchozí namespace entit (`$defaultEntityNamespace`) v `LeanMapper\DefaultMapper` se nyní nastavuje pomocí konstruktoru:

```php
$mapper = new LeanMapper\DefaultMapper('App\Entities');
```

Není tedy již nutné podědit `DefaultMapper` a přepsat property `$defaultEntityNamespace`.


## LeanMapper\Fluent

`LeanMapper\Fluent` již nepřepisuje statickou proměnnou `$masks` a spoléhá se na nastavení z Dibi.


## Striktnější přiřazování hodnot

Entity jsou nyní striktnější při přiřazování hodnot do entity. Dřívě bylo možné přiřadit do položky typu `int` např. řetězec a Lean Mapper na pozadí provedl konverzi hodnoty na správný typ pomocí `settype()`. Nyní entita vyhodí vyjímku a přetypování musíte provést sami. Kontrola se neprovádí u položek s příznakem `m:passThru` - u nich musíte kontrolu provést sami.


## Opravy chyb

* byly doplněny testy a opraveny některé okrajové případy
* `Repository` při přístupu k neexistující property (`$repository->unexists`) generovalo nesmyslnou hlášku, nyní vyhazuje vyjímku
* byla opravena kompatibilita s `nette/di` ^3.0 při použití `LeanMapper\Bridges\Nette\DI\LeanMapperExtension`
