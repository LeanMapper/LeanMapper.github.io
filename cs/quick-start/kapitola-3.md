---
title: Kapitola III – Definice entit
redirect_from: "/quick-start/kapitola-3"
---

* [Úvod](#page-title)
* [Entity Tag a Borrower](#toc-entit-tag-a-borrower)
* [Entita Book](#toc-entita-book)
* [Entita Author](#toc-entita-author)
* [Entita Borrowing](#toc-entita-borrowing)


Entita reprezentuje jakýsi *objekt reálného světa*. Nejsnáze lze význam entit pochopit na příkladech: budete-li programovat školní informační systém, pravděpodobně budete mít entity Student, Teacher, Subject a podobné; budete-li programovat informační systém pro autoservis, pravděpodobně budete mít entity Car, Client, Order a tak podobně.

V quick startu si představíme nejstručnější způsob, jak lze entity v Lean Mapperu nadefinovat – pomocí anotací (v [části dokumentace věnované entitám](/cs/docs/entity/) je popsán i alternativní způsob pomocí metod).


## Entity Tag a Borrower {#toc-entit-tag-a-borrower}

![Entity Tag a Borrower](/img/qs-schema-tag+borrower.png)

``` php
<?php

namespace Model\Entity;

/**
 * @property int $id
 * @property string $name
 */
class Tag extends \LeanMapper\Entity
{
}

/**
 * @property int $id
 * @property string $name
 */
abstract class Person extends \LeanMapper\Entity
{
}

class Borrower extends Person
{
}
```

Entita `Borrower` a její abstraktní základ `Person` demonstrují plně podporovanou dědičnost. Jak je z příkladu patrné, dědí se i anotace. Dědičnost zde nepoužíváme úplně samoúčelně, uvidíte, že třídu `Person` ještě jednou využijeme. :)


## Entita Book {#toc-entita-book}

![Entita Book](/img/qs-schema-book.png)

Složitější (ale také zajímavější) je entita `Book`:

``` php
<?php

namespace Model\Entity;

/**
 * @property int $id
 * @property Author $author m:hasOne
 * @property Author|null $reviewer m:hasOne(reviewer_id)
 * @property Borrowing[] $borrowings m:belongsToMany
 * @property Tag[] $tags m:hasMany
 * @property string $pubdate
 * @property string $name
 * @property string|null $description
 * @property string|null $website
 * @property bool $available
 */
class Book extends \LeanMapper\Entity
{
}
```

***TIP:** SQLite, které v quick startu používáme, nemá samostatný datový typ pro datum a čas a i knihovna dibi vrací při použití SQLite driveru datum a čas jako textový řetězec. Samozřejmě by bylo možné (a i vhodné) nadefinovat v této entitě položku pubdate pomocí metod obsahujících konverzi z/do DateTime, ale v quick startu nebudeme do takových detailů zacházet. Dodejme, že celá věc by byla snazší při použití databáze MySQL a MySQL dibi driveru, který datum vrací jako instanci DibiDateTime.*


### 1:N vazby

Součástí `@property` anotací mohou být i definice vazeb mezi entitami. U položky `$author` říkáme, že bude obsahovat související entitu Author – ta bude reprezentovat autora knihy (vztah autorů a knih je typu 1:N), a u položky `$reviewer` říkáme, že bude také moci obsahovat související entitu `Author`, ale k provázání se použije sloupec reviewer_id – tato entita bude reprezentovat recenzenta knihy (opět se jedná o vztah typu 1:N).

Všimněte si, že kniha může existovat klidně i bez recenzenta (položka `$reviewer` je typu `Author|null`). Takto přirozeně lze v Lean Mapperu vyjádřit, že nějaká položka může obsahovat `null`. Platí, že ať už uvedete `Author|null`, nebo `null|Author` nebo `NULL|Author`… Lean Mapper vám bude rozumět. Můžete psát prostě tak, jak jste zvyklí.

U položky `$author` vazební sloupec uvádět nemusíme, protože jeho název dodržuje konvence Lean Mapperu (blíže popsané v [dokumentaci](/cs/docs/konvence/)). Tyto konvence není nutné dodržovat, Lean Mapper je dobře připraven na nejrůznější odchylky, ale v takovém případě je nutné počítat s o něco více psaním. Konvence je tedy vhodné dodržovat všude tam, kde to je možné.

***TIP:** Všimněte si, že Lean Mapper rozumí nejen definici jmenného prostoru, ale i použitým use statementům.*


### M:N vazby

Typickou definici vazby typu M:N můžete vidět u položky `$tags`. Všimněte si, jak se v Lean Mapperu definuje datový typ „kolekce“ (zde `Tag[]`).


### Další vazby

Příznak `m:belongsToMany` u položy `$borrowings` definuje vazbu typu 1:N pozorovanou z druhého konce. Umožňuje nám pro vybranou knihu snadno získat související záznamy v tabulce borrowing. Tato tabulka obsahuje jakousi evidenci výpůjček – kdo (`Borrower`) si kdy vypůjčil jakou knihu (`Book`). Tento typ vazby se v praxi nesmírně často hodí!


## Entita Author {#toc-entita-author}

![Entita Author](/img/qs-schema-author.png)

Následující entita bude reprezentovat autora (připomeňme si, že autoři mohou být také recenzenty):

``` php
<?php

namespace Model\Entity;

/**
 * @property Book[] $books m:belongsToMany
 * @property Book[] $reviewedBooks m:belongsToMany(reviewer_id)
 * @property string|null $web
 */
class Author extends Person
{

	public function getReferencingTags()
	{
		$tags = array();
		foreach (array(null, 'reviewer_id') as $viaColumn) {
			foreach ($this->row->referencing('book', $viaColumn) as $book) {
				foreach ($book->referencing('book_tag') as $tagRelation) {
					$row = $tagRelation->referenced('tag');
					$tags[$tagRelation->tag_id] = new Tag($row);
				}
			}
		}
		return $tags;
	}

}
```

Příznak `m:belongsToMany` u položky `$books` umožňuje získat pro vybraného autora všechny knihy, kterých je autorem, a příznak `m:belongsToMany(reviewer_id)` u položky `$reviewedBooks` umožňuje získat všechny knihy, které recenzoval. Jde opět o vazby typu 1:N pozorované z druhého konce.

Účelem metody `getReferencingTags()` je quick start trochu okořenit. Jedná se o ukázku, jak snadno lze vytvořit speciální getter. Význam této metody si ukážeme na příkladech v další kapitole.


## Entita Borrowing {#toc-entita-borrowing}

![Entita Borrowing](/img/qs-schema-borrowing.png)

Poslední entitou, kterou budeme potřebovat, souvisí s již zmíněnou tabulkou borrowing:

``` php
<?php

namespace Model\Entity;

/**
 * @property int $id
 * @property Book $book m:hasOne
 * @property Borrower $borrower m:hasOne
 * @property string $date
 */
class Borrowing extends \LeanMapper\Entity
{
}
```

***TIP:** Vytvářet samostatné entitní třídy pro běžné spojovací tabulky je bezpředmětné (co víc, je to až nežádoucí). Naše spojovací tabulka ale nese doplňující informace k vazbě a ty se nejsnáze namapují nadefinováním entitní třídy pro samotnou vazbu.*

----------

Předvedli jsme si, jak lze v Lean Mapperu pomocí pár řádků nadefinovat entity a vztahy mezi nimi. Chybí nám tedy už jen něco, co nám umožní entity načítat, vytvářet, persistovat a odstraňovat. To něco jsou **repositáře**.


[« Vytvoření schéma databáze](/cs/quick-start/kapitola-2/) | [Definice repositářů »](/cs/quick-start/kapitola-4/)
