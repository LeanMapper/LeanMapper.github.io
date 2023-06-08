---
title: Filtry
redirect_from: "/dokumentace/filtry"
rank: 50
---

* [Úvod](#page-title)
* [Registrace filtru](#toc-registrace-filtru)
* [Více filtrů](#toc-vice-filtru)
* [Parametry filtrů](#toc-parametry-filtru)
	* [Databázový dotaz](#toc-parametry-filtru-fluent)
	* [Parametry předané přes auto-wiring](#toc-parametry-filtru-auto-wiring)
	* [Adresované parametry](#toc-parametry-filtru-adresovane)
	* [Dynamicky předané parametry](#toc-parametry-filtru-dynamicke)
* [HasMany vazby](#toc-hasmany-vazby)
* [Implicitní filtry](#toc-implicitni-filtry)
* [Objekt Filtering - anonymní filtry](#toc-objekt-filtering)
* [SQL strategie](#toc-sql-strategie)

Filtry umožňují doladit Lean Mapperem připravený databázový dotaz těsně před tím, než se pošle do databáze. Pokud např. máme entitu `Author` a načítáme autorovy knihy (entitu `Book`) pomocí `$author->books`, Lean Mapper připraví potřebný dotaz (objekt `LeanMapper\Fluent`) a dovolí nám v něm cokoli upravit.

Důvodů pro úpravu připraveného dotazu může být mnoho - můžeme výsledek dotazu limitovat, řadit, přijoinovat další tabulku, apod. Fantazii se meze nekladou.

Pojďme k praktické ukázce. Řekněme, že chceme autorovy knihy vždy načítat seřazené podle názvu. Nadefinujeme si tedy entity `Book` a `Author`.

``` php?start_inline=1
/**
 * @property int $id
 * @property string $name
 * @property Author $author m:hasOne
 */
class Book extends LeanMapper\Entity
{
}

/**
 * @property int $id
 * @property string $name
 * @property Book[] $books m:belongsToMany m:filter(bookOrderByName)
 */
class Author extends LeanMapper\Entity
{
}
```

Na entitě `Book` není nic zajímavého, zajímavější je entita `Author`. Všimněte si příznaku `m:filter` u položky `$books`.

Tímto příznakem říkáme Lean Mapperu, že má při získávání kolekce autorových knih zavolat filtr pojmenovaný `bookOrderByName`. Aby to fungovalo, musíme ještě samotný filtr naprogramovat a zaregistrovat.


## Registrace filtru {#toc-registrace-filtru}

Filtrem může být jakákoli volatelná metoda, nebo funkce (klidně anonymní). Filtr zaregistrujeme pomocí metody `LeanMapper\Connection::registerFilter()`, které předáme název filtru a odkaz na zvolenou metodu/funkci.

``` php?start_inline=1
$connection->registerFilter('bookOrderByName', ['BookFilter', 'orderByName']);
```

Uvedený řádek zaregistruje filtr `bookOrderByName` a zároveň říká, že se má při použití tohoto filtru zavolat statická metoda `BookFilter::orderByName`. Samotný filtr bude úplně jednoduchý, jen přidá do připraveného dotazu klauzuli `ORDER BY` s názvem sloupce:

``` php?start_inline=1
class BookFilter
{
	public static function orderByName(LeanMapper\Fluent $fluent)
	{
		$fluent->orderBy('[name]');
	}
}
```


## Více filtrů {#toc-vice-filtru}

Filtry v příznaku `m:filter` můžeme zřetězit - Lean Mapper filtry zavolá jeden po druhém. Jednotlivé filtry se oddělují čárkou.

```
 * @property Book[] $books m:belongsToMany m:filter(bookOrderByName, bookOnlyAvailable)
```

Uvedená definice způsobí, že na dotaz bude kromě filtru `bookOrderByName` aplikován i filtr `bookOnlyAvailable`.


## Parametry filtrů {#toc-parametry-filtru}

Kromě instance `LeanMapper\Fluent` může filtr obdržet i další parametry. Pojďme si je probrat v pořadí, ve kterém jsou filtru předány.


### Databázový dotaz {#toc-parametry-filtru-fluent}

Prvním parametrem filtru je vždy instance objektu `LeanMapper\Fluent`, která obsahuje předpřipravený dotaz.


### Parametry předané přes auto-wiring {#toc-parametry-filtru-auto-wiring}

Další parametry můžou obsahovat entitu a property (`LeanMapper\Reflection\Property`), u které byl filtr zapsán. Aby k předání došlo, musíme při registraci filtru uvést jednu z konstant `LeanMapper\Connection::WIRE_*`.

``` php?start_inline=1
$connection->registerFilter('bookOrderByName', ['BookFilter', 'orderByName'], LeanMapper\Connection::WIRE_ENTITY_AND_PROPERTY);
```

Uvedený zápis řekne Lean Mapperu, že má filtru předat ve druhém parametru entitu a ve třetím property:

``` php?start_inline=1
class BookFilter
{
	public static function orderByName(LeanMapper\Fluent $fluent, LeanMapper\Entity $entity, LeanMapper\Reflection\Property $property)
	{
		// ...
	}
}
```

Dalšími možnými hodnotami jsou:

* `LeanMapper\Connection::WIRE_ENTITY` - předá jen entitu
* `LeanMapper\Connection::WIRE_PROPERTY` - předá jen property

**Poznámka:** místo konstant lze použít i textové alternativy - znak `e` odpovídá konstantě `WIRE_ENTITY`, znak `p` konstantě `WIRE_PROPERTY` a ekvivaletnem ke konstantě `WIRE_ENTITY_PROPERTY` jsou řetězce `ep` a `pe`.


### Adresované parametry {#toc-parametry-filtru-adresovane}

Další parametry filtru můžou obsahovat tzv. adresované parametry. To jsou hodnoty, které uvedeme přímo v definici entity a vztahují se vždy ke konkrétnímu filtru. Obecný filtr pro limitování výsledků by tedy mohl vypadat takto:

``` php?start_inline=1
class CommonFilter
{
	public static function limit(LeanMapper\Fluent $fluent, $limit)
	{
		$fluent->limit($limit);
	}
}

$connection->registerFilter('limit', ['CommonFilter', 'limit']);


/**
 * @property int $id
 * @property string $name
 * @property Book[] $books m:belongsToMany m:filter(limit#10)
 */
class Author extends LeanMapper\Entity
{
}
```

Při volání `$author->books` tedy obdržíme vždy pouze prvních 10 knih.

***Poznámka:** uvedený příklad nebude fungovat zcela podle očekávání - viz kapitola [SQL strategie](../sql-strategie/).*


### Dynamicky předané parametry {#toc-parametry-filtru-dynamicke}

Na závěr můžou následovat parametry dynamicky předané volajícím pomocí getteru. Hodnoty budou předány do všech filtrů uvedených v příznaku `m:filter`. Při použití výše uvedeného filtru pro limitování výsledků to může vypadat nějak takto:

``` php?start_inline=1
class CommonFilter
{
	public static function limit(LeanMapper\Fluent $fluent, $limit)
	{
		$fluent->limit($limit);
	}
}

$connection->registerFilter('limit', ['CommonFilter', 'limit']);


/**
 * @property int $id
 * @property string $name
 * @property Book[] $books m:belongsToMany m:filter(limit)
 */
class Author extends LeanMapper\Entity
{
}

$books = $author->getBooks(20); // získá prvních 20 knih
```

***Poznámka:** uvedený příklad nebude fungovat zcela podle očekávání - viz kapitola [SQL strategie](../sql-strategie/).*


## HasMany vazby {#toc-hasmany-vazby}

V případě `hasMany` vazeb se pokládají celkem 2 dotazy - jeden načítá data ze spojovací tabulky a další z cílové tabulky. Pomocí filtrů můžeme ovlivnit oba položené dotazy, stačí filtry v příznaku `m:filter` oddělit pomocí svislítka `|`.

``` php?start_inline=1
class CommonFilter
{
	public static function limit(LeanMapper\Fluent $fluent, $limit)
	{
		$fluent->limit($limit);
	}


	public static function orderBy(LeanMapper\Fluent $fluent, $column)
	{
		$fluent->orderBy('%n', $column);
	}
}

$connection->registerFilter('limit', ['CommonFilter', 'limit']);
$connection->registerFilter('orderBy', ['CommonFilter', 'orderBy']);


/**
 * @property int $id
 * @property string $name
 */
class Tag extends LeanMapper\Entity
{
}

/**
 * @property int $id
 * @property string $name
 * @property Tag[] $tags m:hasMany m:filter(limit#10|orderBy#name)
 */
class Book extends LeanMapper\Entity
{
}
```

Výše uvedený příklad načte pomocí `$book->tags` prvních 10 tagů souvisejících s knihou a seřadí je podle názvu. Všimněte si příznaku `m:filter` - pomocí filtru `limit` nejprve limitujeme dotaz do tabulky `book_tag` a následně upravujeme dotaz do tabulky `tag` pomocí filtru `orderBy` tak, aby vrátil tagy seřazené podle jména.

***Poznámka:** uvedený příklad nebude fungovat zcela podle očekávání - viz kapitola [SQL strategie](../sql-strategie/).*


## Implicitní filtry {#toc-implicitni-filtry}

Výchozí, neboli implicitní, filtry jsou takové filtry, které budou aplikovány vždy bez ohledu na to, jestli jsou uvedeny v příznaku `m:filter`, či nikoli. Implicitní filtry se spouští při traverzování mezi entitami a z repozitáře v rámci metody `LeanMapper\Repository::createFluent()` - kvůli tomu nemůžou obdržet parametry předávané pomocí [auto-wiringu](#toc-parametry-filtru-auto-wiring). Využití naleznou např. pro soft-deleted entity a další podobné případy.

K definici implicitních filtrů slouží metoda `LeanMapper\IMapper::getImplicitFilters`, která vrací buď pole s názvy filtrů, nebo instanci objektu `LeanMapper\ImplicitFilters` - ta může kromě názvů obsahovat i [adresované parametry](#toc-parametry-filtru-adresovane), které se mají filtrům předat.

Pojďme si to ukázat v praxi - řekněme, že tabulka knih obsahuje sloupec `available`, který indikuje, zda je kniha dostupná, či nikoli a nabývá hodnot `0` a `1`. Při načítání knih kdekoli v aplikaci pak chceme, aby se načetly jenom dostupné knihy.

``` php?start_inline=1
/**
 * @property int $id
 * @property string $name
 * @property bool $available
 */
class Book extends LeanMapper\Entity
{
}

/**
 * @property int $id
 * @property string $name
 * @property Book[] $books m:belongsToMany
 */
class Author extends LeanMapper\Entity
{
}

class CommonFilter
{
	public static function restrictAvailables(LeanMapper\Fluent $fluent, $table)
	{
		$fluent->where('%n.[available] = 1', $table);
	}
}

$connection->registerFilter('restrictAvailables', ['CommonFilter', 'restrictAvailables']);

class Mapper extends LeanMapper\DefaultMapper
{
	public function getImplicitFilters($entityClass, LeanMapper\Caller $caller = null)
	{
		if ($entityClass === 'Book') {
			return new LeanMapper\ImplicitFilters(
				['restrictAvailables'],
				[
					'restrictAvailables' => [ // parametry pro filtr 'restrictAvailables'
						$this->getTable($entityClass),
					],
				]
			);
		}
		return parent::getImplicitFilters($entityClass, $caller);
	}
}

$mapper = new Mapper;
$books = $author->books;
```

Nejprve definujeme entity `Book` a `Author`, poté si vytvoříme obecný filtr `CommonFilter::restrictAvailables`. Nejdůležitější je v tomto případě vlastní mapper - v něm definujeme metodu `getImplicitFilters`, která pro entitu `Book` vytvoří instanci objektu `LeanMapper\ImplicitFilters` - té předá nejprve seznam filtrů (zde pouze filtr `restrictAvailables`) a následně i adresovaný parametr s názvem tabulky, na kterou má být omezení aplikováno. Díky tomu nám bude volání `$author->books` vždy vracet jenom dostupné knihy.

Metoda `getImplicitFilters` může kromě názvu entity obdržet ještě nepovinný parametr `$caller`. Implicitní filtry jsou obvykle aplikovány při volání metody `Repository::createFluent` - pak parametr `$caller` obsahuje odkaz na repositář, nebo při traverzování mezi entitami - v tom případě parametr `$caller` obsahuje odkaz na entitu a property, přes kterou se traverzuje.


## Objekt Filtering - anonymní filtry {#toc-objekt-filtering}

Filtry lze použít nejen pomocí příznaku `m:filter` a implicitních filtrů, ale i ve vlastních přístupových metodách uvnitř entit. Stačí vytvořit instanci objektu `LeanMapper\Filtering` a tu předat do volaných metod - např. do metody `getValueByPropertyWithRelationship`. Vytvoříme tak něco, co se dá nazvat anonymními filtry.

``` php?start_inline=1
/**
 * @property int $id
 * @property string $name
 * @property Book[] $books m:belongsToMany(#union)
 */
class Author extends LeanMapper\Entity
{
	public function getNewestBook()
	{
		$books = $this->getValueByPropertyWithRelationship('books', new LeanMapper\Filtering(function (LeanMapper\Fluent $fluent) {
			$fluent->orderBy('pubdate')->desc()
				->limit(1);
		}));
		return empty($books) ? null : reset($books);
	}
}
```

Volání `$author->getNewestBook()` vrátí vždy nejnovější autorovu knihu.

***Poznámka:** v uvedeném příkladu používáme u příznaku `m:belongsToMany` dovětek `#union`. Účel tohoto dovětku popisuje kapitola [SQL strategie](../sql-strategie/).*


## SQL strategie {#toc-sql-strategie}

S filtry souvisí tzv. [SQL strategie](../sql-strategie/). Blíže se tomuto tématu věnujeme v samostané [kapitole](../sql-strategie/).

[« Persistence](/cs/docs/persistence/) | [Mapper »](/cs/docs/mapper/)
