---
title: Entity
---

* [Úvod](#page-title)
* [Položky](#toc-polozky)
	* [Definice pomocí anotací](#toc-definice-pomoci-anotaci)
		* [Vazby v anotacích](#toc-vazby-v-anotacich)
	* [Definice pomocí metod](#toc-definice-pomoci-metod)
	* [Přístup k položkám](#toc-pristup-k-polozkam)
	* [Hromadné přiřazování](#toc-hromadne-prirazovani)
	* [Priorita definic](#toc-priorita-definic)


Entity reprezentují potomci abstraktní třídy [`LeanMapper\Entity`](http://leanmapper.com/api/v1.3.1/class-LeanMapper.Entity.html). Ta obsahuje definici magických metod `__get`, `__set` a `__call`, které usnadňují přístup k položkám entity, a dále obsahuje elementární sadu metod užitečných v různých situacích. V neposlední řadě tato třída udržuje i instanci [`LeanMapper\Row`](http://leanmapper.com/api/v1.3.1/class-LeanMapper.Row.html), která zapouzdřuje vlastní data.


## Položky {#toc-polozky}

Položky entity lze nadefinovat dvěma způsoby: pomocí *anotací* a pomocí *metod*.


### Definice pomocí anotací {#toc-definice-pomoci-anotaci}

Preferovaným (a stručnějším) způsobem, jak nadefinovat položky entity, je pomocí anotací. Tímto způsobem lze v praxi běžně nadefinovat naprostou většinu položek a vazeb. K definici pomocí metod má obvykle smysl přistoupit pouze v případě několika málo specifických položek, jejichž nadefinování pomocí anotací není možné (z důvodu jejich nedostatečné vyjadřovací schopnosti).

Možnosti anotací nejsnáze popíšeme ukázkou:

``` php
<?php

namespace Model\Entity;

use DateTime as PlaceInTime;

/**
 * @property int $id
 * @property PlaceInTime $published
 * @property Foo|null $foo m:hasOne
 * @property Bar[] $bars m:hasMany(:::bartable)
 * @property string $title
 * @property string|null $description
 * @property null|int $number
 * @property bool $active
 */
class DummyEntity extends \LeanMapper\Entity
{
}
```

Příznakům `m:hasOne` a `m:hasMany` nemusíte nyní věnovat pozornost, jejich význam si brzy vysvětlíme.

Definice každé položky se sestává z anotace `@property` následované typem položky, názvem a případnými volitelnými příznaky týkajících se vazeb a filtrů. Součástí definice typu může být volitelně i příznak, zda může položka obsahovat hodnotu `null`.

Všimněte si použitého `use` statementu. Lean Mapper rozumí nejen definici jmenného prostoru, ale i použitým use statementům. Snad jediná záludnost, se kterou si neporadí, je přítomnost více jmenných prostorů v jednom souboru. Máte-li potřebu této vlastnosti PHP využít, nadefinujte typy položek v @property anotacích jako „fully qualified“ (tj. uvozených zpětným lomítkem).
{:#use}

Nepřehlédněte také, jakým způsobem se definují „kolekce“ (pole s hodnotami určitého typu) – pomocí prázdných hranatých závorek bezprostředně za typem položky (v naší ukázce `Bar[]`).


#### Vazby v anotacích {#toc-vazby-v-anotacich}

Lean Mapper rozumí celkem čtyřem příznakům pro vyjádření vazeb mezi entitami: `m:hasOne`,`m:hasMany`, `m:belongsToOne`, `m:belongsToMany`. Tyto příznaky se vepisují za název položky a volitelným dodatkem v závorce lze upřesnit, přes jaké sloupce a tabulky má Lean Mapper při získávání související entity postupovat. Pokud dodatek chybí, Lean Mapper si potřebné názvy odvozuje podle [daných konvencí](/cs/docs/konvence/). Pokud vaše databáze tyto konvence dodržuje, v naprosté většině případů se bez těchto dodatků obejdete.


| Příznak | Tvar dodatku | Význam | Příklad použití
|---------|--------------|--------|-------------------
| m:hasOne | (sloupec odkazující na cílovou tabulku:cílová tabulka) | Vazba N:1 | Máme knihu a chceme načíst jejího autora.
| m:hasMany | (sloupec odkazující na zdrojovou tabulku:vazební tabulka:sloupec odkazující na cílovou tabulku:cílová tabulka) | Vazba M:N | Máme knihu a chceme získat kolekci tagů, kterými je označena.
| m:belongsToOne | (sloupec odkazující na zdrojovou tabulku:cílová tabulka) | Vazba 1:1 | Máme objednávku a chceme získat detail objednávky uložený v samostatné tabulce. Každý detail patří právě jedné objednávce.
| m:belongsToMany | (sloupec odkazující na zdrojovou tabulku:cílová tabulka) | Vazba 1:N | Máme autora a chceme získat kolekci knih, kterých je autorem.


U dovětků platí, že jejich jednotlivé části jsou odděleny dvojtečkou a vynechání libovolné části vede k jejímu odvození [podle konvencí](/cs/docs/konvence/).


| Nejstručnější zápis | Příklad (ekvivalentního zápisu/ekvivalentních zápisů)
|---------------------|------------------------------------------------------
| Author $author m:hasOne | Author $author m:hasOne(author_id:author)
| Author $author m:hasOne(reviewer_id) | Author $author m:hasOne(reviewer_id:author)
| Tag[] $tags m:hasMany | Tag[] $tags m:hasMany(book_id:book_tag:tag_id:tag)
| | Tag[] $tags m:hasMany(book_id:::tag)
| | Tag[] $tags m:hasMany(book_id::tag_id:tag)
| | Tag[] $tags m:hasMany(book_id::tag_id)
| Tag[] $tags m:hasMany(::supertag_id:supertag) | Tag[] $tags m:hasMany(book_id:book_tag:supertag_id:supertag)
| Book[] $books m:belongsToMany | Book[] $books m:belongsToMany(author_id:author)
| OrderDetail $detail m:belongsToOne | OrderDetail $detail m:belongsToOne(order_id:orderdetail)


### Definice pomocí metod {#toc-definice-pomoci-metod}

Výše uvedenou entitu můžeme kompletně přepsat do následující podoby:

``` php
<?php

namespace Model\Entity;

use DibiDateTime as PlaceInTime;

class DummyEntity extends \LeanMapper\Entity
{

	public function getId()
	{
		return (int) $this->row->id;
	}

	public function setId($id)
	{
		$this->row->id = (int) $id;
	}

	public function getPublished()
	{
		return $this->row->published;
	}

	public function setPublished(PlaceInTime $published)
	{
		$this->row->published = $published;
	}

	public function getFoo()
	{
		$row = $this->row->referenced('foo');
		return $row !== null ? new Foo($row) : null;
	}

	public function setFoo(Foo $foo = null)
	{
		$this->row->foo_id = $foo !== null ? $foo->id : null;
		$this->row->cleanReferencedRowsCache('foo', 'foo_id');
	}

	public function getBars()
	{
		$value = array();
		foreach ($this->row->referencing('dummy_bar') as $row) {
			$value[] = new Bar($row->referenced('bartable'));
		}
		return $value;
	}

	public function getTitle()
	{
		return (string) $this->row->title;
	}

	public function setTitle($title)
	{
		$this->row->title = (string) $title;
	}

	public function getDescription()
	{
		$description = $this->row->description;
		return $description !== null ? (string) $description : null;
	}

	public function setDescription($description)
	{
		$this->row->description = $description !== null ? (string) $description : null;
	}

	public function getNumber()
	{
		$number = $this->row->number;
		return $number !== null ? (int) $number : null;
	}

	public function setNumber($number)
	{
		$this->row->number = $number !== null ? (int) $number : null;
	}

	public function getActive()
	{
		return (bool) $this->row->active;
	}

	public function setActive($active)
	{
		$this->row->active = (bool) $active;
	}

}
```

Jedná se o transparentní, „nemagickou“ variantu, avšak patřičně upovídanou. Na druhou stranu, vyjadřovací schopnosti metod jsou větší než anotací, a proto občas není jiného východiska, než metody použít. Oba způsoby definic položek lze samozřejmě kombinovat, dokonce i v rámci jedné entity.

Všimněte si, že ve všech předvedených metodách se přistupuje k protected proměnné `$row`. Entity v Lean Mapperu totiž vlastní data neudržují. Udržují jen instanci třídy [`LeanMapper\Row`](http://leanmapper.com/api/v1.3.1/class-LeanMapper.Row.html), která vlastní data zapouzdřuje. Pro zájemce uveďme, že vlastní data neudržuje ani instance `LeanMapper\Row`. Ta vystupuje jen jako ukazatel na konkrétní sadu hodnot uvnitř instance [`LeanMapper\Result`](http://leanmapper.com/api/v1.3.1/class-LeanMapper.Result.html). Vlastní data musíme hledat až v ní.

Toto zdánlivě kostrbaté řešené umožňuje pokládat efektivní dotazy do databáze (ve stylu „NotORM“). Při typickém používání vás ale zmíněné interní záležitosti Lean Mapperu vůbec nemusí trápit. Seznamte se jen s veřejným rozhraním třídy [`LeanMapper\Row`](http://leanmapper.com/api/v1.3.1/class-LeanMapper.Row.html), abyste měli představu, co všechno se s jejími instancemi v entitách dá dělat.


### Přístup k položkám {#toc-pristup-k-polozkam}

Následující ukázka demonstruje, jak lze k položkám entity přistupovat:

``` php
<?php

// $entity instanceof Model\Entity\DummyEntity

$entity->id;

$entity->title;

$entity->description;

$entity->getDescription();

$entity->title = 'New title';

$entity->setDescription('lorem ipsum');
```


### Hromadné přiřazování {#toc-hromadne-prirazovani}

Užitečnou metodou je metoda `assign(array $values, array $whitelist = null)`, kterou entity dědí z [`LeanMapper\Entity`](http://leanmapper.com/api/v1.3.1/class-LeanMapper.Entity.html). Umožňuje hromadně přiřadit hodnoty do více položek:

``` php
<?php

$entity->assign(array(
	'title' => 'Modified title',
	'description' => 'lorem ipsum',
	'number' => 42,
));

$entity->assign($newValues, array('title', 'description'));
```

Prvním parametrem je pole ve formátu položka => nová hodnota a druhým, volitelným, je „whitelist“ položek, které se berou v úvahu. Pokud je metoda volána se dvěma parametry, pozměněné hodnoty položek, které nejsou vyjmenovány ve „whitelistu“, se ignorují.

Pokud vámi používaný framework například umí vracet hodnoty z formulářů v podobě pole, váš kód může být takto stručný:

``` php
<?php

$author->assign($form->getValues(), array('title', 'name', 'web');
```

Nutno zdůraznit, že se jedná jen o jakýsi syntaktický cukr a pod pokličkou se volají jednotlivé settery entity, které obvykle obsahují validační pravidla. Jedná se tedy o naprosto bezpečný a legitimní způsob práce s entitou.


### Priorita definic {#toc-priorita-definic}

Při přístupu k `$book->title` se postupuje následovně:

1. Hledá se anotace `@property string $title`. Pokud existuje, Lean Mapper ji použije a vrátí relevantní hodnotu.
2. Pokud anotace neexistuje, Lean Mapper hledá metodu `getTitle()`. Pokud existuje, zavolá ji a vrátí její návratovou hodnotu.
3. Pokud ani tato metoda neexistuje, Lean Mapper vyvolá výjimku `LeanMapper\Exception\MemberAccessException`.

Při přístupu k `$book->getTitle()` se postupuje následovně:

1. Hledá se metoda `getTitle()`. Pokud existuje, Lean Mapper ji zavolá a vrátí její návratovou hodnotu.
2. Pokud metoda neexistuje, Lean Mapper hledá anotaci `@property string $title`. Pokud existuje, použije ji a vrátí relevantní hodnotu.
3. Pokud ani tato anotace neexistuje, Lean Mapper vyvolá výjimku `LeanMapper\Exception\MemberAccessException`.

Při volání `$book->title = 'New title'` se postupuje následovně:

1. Hledá se anotace `@property string $title`. Pokud existuje, Lean Mapper ji použije a nastaví požadovanou hodnotu.
2. Pokud anotace neexistuje, Lean Mapper hledá metodu `setTitle($title)` (parametr může mít libovolný název). Pokud existuje, zavolá ji a přiřazovanou hodnotu jí předá jako argument.
3. Pokud ani tato metoda neexistuje, Lean Mapper vyvolá výjimku `LeanMapper\Exception\MemberAccessException`.

Při volání `$book->setTitle('New title')` se postupuje následovně:

1. Hledá se metoda `setTitle($title)` (parametr může mít libovolný název). Pokud existuje, Lean Mapper ji zavolá a přiřazovanou hodnotu jí předá jako argument.
2. Pokud metoda neexistuje, Lean Mapper hledá anotaci `@property string $title`. Pokud existuje, použije ji a nastaví požadovanou hodnotu.
3. Pokud ani tato anotace neexistuje, Lean Mapper vyvolá výjimku `LeanMapper\Exception\MemberAccessException`.

Jak je z pravidel vidět, při přístupu přes magickou metodu `__get` mají přednost anotace, při přístupu přes metody mají přednost metody.


[« Úvod do dokumentace](/cs/docs/) | [Repositáře »](/cs/docs/repositare/)
