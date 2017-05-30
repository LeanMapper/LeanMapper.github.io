---
title: SQL strategie
---

* [Úvod](#page-title)
* [SQL strategie a filtry](#toc-filtry)


Lean Mapper sestavuje dotazy „v duchu NotORM“, a proto načítá jedním dotazem související knihy pro všechny autory, které jsme získaly např. při volání `$authorRepository->fetchAll()`. Podle *výchozí strategie* tedy připraví následující dotaz (vyobrazená ID samozřejmě závisí na konkrétních datech v databázi):

``` sql
SELECT `book`.* FROM `book` WHERE `book`.`author_id` IN (11, 12)
```

Všimněte si operátoru `IN` v SQL dotazu - díky němu je výchozí strategie nazývána jako *„IN strategie“*.

Pokud bychom do tohoto dotazu přidali, např. pomocí [filtru](../filtry/), klauzuli `LIMIT 1`, dostaneme jiný výsledek, než jaký jsme původně zamýšleli. Nechceme limitovat výsledek s knihami pro všechny autory. To, co potřebujeme, je, aby Lean Mapper položil do databáze trochu odlišný dotaz:

``` sql
(SELECT `book`.* FROM `book` WHERE `book`.`author_id` = 11 LIMIT 1)
UNION
(SELECT `book`.* FROM `book` WHERE `book`.`author_id` = 12 LIMIT 1)
```

Díky tomu získáme to, co jsme chtěli - jednu knihu pro každého autora.

Tento způsob dotazování nazýváme jako *„UNION strategii“*. UNION strategii aktivujeme u konkrétních [vazeb](../entity/#toc-vazby-v-anotacich) `m:hasMany`, `m:belongsToMany` a `m:belongsToOne` pomocí dovětku `#union`. Výše uvedený příklad s limitováním knih bychom zapsali takto:

``` php?start_inline=1
class CommonFilter
{
	public static function limit(LeanMapper\Fluent $fluent, $limit)
	{
		$fluent->limit($limit);
	}
}

$connection->registerFilter('limit', array('CommonFilter', 'limit'));


/**
 * @property int $id
 * @property string $name
 * @property Book[] $books m:belongsToMany(#union) m:filter(limit#1)
 */
class Author extends LeanMapper\Entity
{
}
```

Všimněte si dovětku `#union`, který zakončuje definici vazby - validní jsou i zápisy `m:belongsToMany(author_id#union)`, `m:belongsToMany(author_id:author#union)`, apod.


***Poznámka:** Dodejme, že získávání dat přes UNION strategii je zapotřebí jen opravdu zřídka.*


## SQL strategie a filtry {#toc-filtry}

Při použití filtrů je každý filtr aplikován na každou samostatnou část UNION dotazu - viz příklad výše, kdy je filtrem přidána klauzule `LIMIT` do každé části dotazu.


[« Integrace Lean Mapperu do aplikace](/cs/docs/integrace-do-aplikace/) |
