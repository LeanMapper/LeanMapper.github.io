---
title: "Novinky ve verzi 4.1"
author: janpecha
---

{:.perex}
Chystaná verze 4.1 přinese řadu užitečných novinek.


## Použití konstruktoru u entit

Konstruktor entity je aktuálně používán k vytvoření entity z pole dat či databázového řádku:

```php
$book = new Book($row);
```

To však není optimální, pokud chceme konstruktor použít pro vlastní způsob inicializace entity:

```php
$book = new Book($name, $author);
```

Nová verze přináší řešení v podobě traity `Initialize` a úpravě výchozí [EntityFactory](/cs/docs/entity-factory/), která zajistí, že entita, která tuto traitu používá bude při načítání dat z databáze vytvářena odlišným způsobem bez volání konstruktoru.

Stačí jen v entitě (či předkovi) traitu použít (a nezapomenout v konstrukturu zavolat `parent::__construct()`):

```php
/**
 * @property int $id
 * @property string $name
 * @property Author $author m:hasOne
 */
class Book extends \LeanMapper\Entity
{
	use \LeanMapper\Initialize;


	public function __construct(
		string $name,
		Author $author
	)
	{
		parent::__construct();

		$this->name = $name;
		$this->author = $author;
	}
}
```


## Podpora pro typ `non-empty-string`

U položek entity je nově možné kromě typu `string` používat i typ `non-empty-string`. Lean Mapper pak vynucuje, aby předaná hodnota byla skutečně neprázdný řetězec.

```php
/**
 * @property non-empty-string $name
 */
class Author extends \LeanMapper\Entity
{
}
```

Je to první krok k podpoře šiřšího spektra typů v položkách entit.


## Označení nullable položek v entitě

Drobně vylepšen byl parser typů v anotacích, který kromě zápisu `Foo|null` nově u nullable položek rozumí i zápisu `?Foo`.

```php
/**
 * @property ?Author $author m:hasOne
 * @property ?int $year
 */
class Book extends \LeanMapper\Entity
{
}
```


## Vylepšení pro statickou analýzu

Drobné úpravy doznalo i rozhraní `IEntityFactory`, u kterého byla lépe specifikována návratová hodnota z metody `createCollection()` - původní `Entity[]` bylo nahrazeno za `iterable<Entity>`, které by mělo lépe odpovídat realitě.

V souvislosti s tím došlo ke stejné úpravě i u metod `Entity::getHasManyValue()`, `Entity::getBelongsToManyValue()` a `Repository::createEntities()`.

----

**Verze 4.1 je aktuálně v RC fázi - vyzkoušejte ji prosím na svých projektech, pokud se neobjeví žádná komplikace, vyjde cca za týden stabilní verze.**
