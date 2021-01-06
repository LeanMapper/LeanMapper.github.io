---
title: Otestujte Lean Mapper 4
author: janpecha
---

{:.perex}
Lean Mapper 4 je za dveřmi, pojďte ho otestovat.

Poslední vydanou verzí byl Lean Mapper 3.4.2 vydaný v dubnu 2020. Dnes si ukážeme co přináší chystaný Lean Mapper 4.


## Podpora Dibi 4 a PHP 7.1+

Dlouho očekávaná změna - Lean Mapper nyní vyžaduje Dibi 4 a PHP 7.1 nebo novější. Zároveň by měl být Lean Mapper plně kompatibilní s **PHP 8.0**.

S tím souvisí největší BC break této verze - všechny metody a funkce nyní obsahují nativní typehinty, které přineslo PHP 7.1. Taky používáme striktní typování (tj. všechny soubory obsahují `declare(strict_types=1)`).

Pokud tedy používáte např. vlastní mapper, budete muset doplnit typehinty k parametrům, aby byl mapper kompatibilní s rozhraním `LeanMapper\IMapper`.


## Konverze hodnot z databáze

Hlavní novinka této verze. Pokud chcete, máte nyní možnost upravit hodnoty, které přicházejí z databáze před tím než se předají do entity (včetně zpětné konverze ve směru entita => databáze).

Otevírá to cestu k různým konverzím hodnot (`string` na `DateTime`) nebo nativnímu použití value objektů (můžeme např. snadno převést emailovou adresu uloženou v databázi na objekt `EmailAddress`). Dříve to šlo realizovat jen pomocí vlastního getteru nebo příznaku `m:passThru`, což nebylo příliš efektivní.

Konverze se realizuje v mapperu pomocí nově přidaných metod `convertToRowData` (databáze => entita) a `convertFromRowData` (entita => databáze):

```php
/**
 * @property int $id
 * @property EmailAddress $email
 */
class Author extends LeanMapper\Entity
{
}


class MyMapper extends LeanMapper\DefaultMapper
{
    public function convertToRowData(string $table, array $values): array
    {
        if ($table === 'author') {
            $values['email'] = new EmailAddress($values['email']);
        }
        return $values;
    }


    public function convertFromRowData(string $table, array $data): array
    {
        if ($table === 'author' && array_key_exists('email', $data)) {
            $data['email'] = $data['email']->getValue();
        }
        return $data;
    }
}
```

V metodě `convertFromRowData()` je nutné kontrolovat, jestli `$data` opravdu obsahují konkrétní hodnotu (v tomto případě `email`). Lean Mapper totiž persistuje pouze změněné hodnoty, takže i metoda `convertFromRowData()` obdrží na vstupu pouze změněná data.

**Pozor!** Tato funkce má zatím jedno omezení - nelze tímto způsobem převádět primární klíče, ty stále musí mít skalární hodnotu (`int|string`). Pokud se to ukáže jako zásadní omezení, bude podpora pro primární klíče doplněna v jednom z příštích vydání.


## Ostatní změny

Jednou ze zbývajících změn je, že se výchozí namespace entit (`$defaultEntityNamespace`) v `LeanMapper\DefaultMapper` nyní nastavuje pomocí konstruktoru:

```php
$mapper = new LeanMapper\DefaultMapper('App\Entities');
```

Dřívě bylo nutné podědit `DefaultMapper` a přepsat property `$defaultEntityNamespace`.

Další změna se týká `LeanMapper\Fluent`. `Fluent` v dřívějších verzích přepisoval statickou proměnnou `$masks`, aby do `Dibi\Fluent` doplnil podporu pro `UNION`. Tato úprava ale nefungovala spolehlivě, takže byla odstraněna a nyní se plně spoléháme na přednastavené chování Dibi.

A v neposlední řadě proběhla drobná úprava ve třídě `LeanMapper\Entity`, která je díky tomu nyní striktnější při přiřazování hodnot do entity. Dřívě bylo možné přiřadit do položky typu `int` např. řetězec a Lean Mapper na pozadí provedl konverzi hodnoty na správný typ pomocí `settype()`. Nyní v takovém případě entita vyhodí vyjímku a přetypování musíte provést sami. Kontrola typu se neprovádí pouze v případě, že položka obsahuje příznak `m:passThru` a Lean Mapper tak spoléhá, že konverzi nebo kontrolu hodnoty provedete sami.


## Opravy chyb

Byly doplněny testy a opraveny některé okrajové případy - např. chyba při volání `Result:: cleanReferencing()/cleanReferenced()` v kombinaci s používáním `FilteringResult`. Troufám si tvrdit, že na většinu těchto případů nikdo nikdy nenarazil.

Dále např. `Repository` při přístupu k neexistující property (`$repository->unexists`) generovalo nesmyslnou hlášku, nyní v takovém případě vyhazuje vyjímku.

A opravena byla i kompatibilita s `nette/di` ^3.0 při použití Nette DI extension (třída `LeanMapper\Bridges\Nette\DI\LeanMapperExtension`).


## Změny pod kapotou

Pod kapotou taky došlo k řadě úprav - byl sjednocen coding style, kód je testován pomocí PhpStan (level 7), apod.


## Pojďte testovat

Lean Mapper 4 je z mého pohledu připraven k vydání. Před tím než tak učiním vás ale prosím, abyste novou verzi otestovali na svých aplikacích a hlásili problémy, na které narazíte. Pořád je ještě čas některé věci před vydáním nové verze upravit. Mělo by stačit, pokud Composeru řeknete, aby instaloval verzi `^4.0@dev`.
