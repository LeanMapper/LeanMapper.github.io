---
title: Entity
---

* [Úvod](#page-title)
* [Definice položek](#toc-polozky)
	* [Definice pomocí anotací](#toc-definice-pomoci-anotaci)
	* [Definice pomocí přístupových metod](#toc-definice-pomoci-metod)
	* [Priorita definic](#toc-priorita-definic)
* [Rozhraní entity](#toc-rozhrani-entity)
    * [Přístup k položkám](#toc-pristup-k-polozkam)
    * [Hromadné přiřazování](#toc-hromadne-prirazovani)
* [Definice vazeb (vztahů mezi entitami) a strategie](#toc-vazby-v-anotacich)
* [Příznaky m:passThru, m:enum](#toc-priznaky-passthru-enum)
* [Správa jednoduchých M:N vazeb](#toc-sprava-mn-vazeb)
* [Defaultní (výchozí) hodnoty](#toc-vychozi-hodnoty)
* [Vlastní příznaky](#toc-vlastni-priznaky)


Entity reprezentují potomci abstraktní třídy `LeanMapper\Entity`. Ta obsahuje definici magických metod `__get`, `__set` a `__call`, které usnadňují přístup k položkám entity, a dále obsahuje elementární sadu metod užitečných v různých situacích. V neposlední řadě tato třída udržuje i instanci `LeanMapper\Row`, která zapouzdřuje vlastní data.

Smyslem entit je reprezentovat v aplikaci *objekty reálného světa* (označované občas honosněji jako *doménové objekty*, kde *doménou* rozumíme oblast zájmu naší aplikace). Dobře se smysl entit vysvětluje názorně: s čím bude operovat náš informační systém pro knihovnu? Určitě s knihami. A také s čtenáři, autory knih, výpůjčkami… a tato **podstatná jména** už nám přímo napovídají, jaké budeme mít v aplikaci entity: `Book`, `Author`, `Borrowing` a určitě by vás napadly i další.

Důležité je, že tyto doménové objekty mají několik typických rysů:

- Mají jasně dané veřejné API (včetně tzv. položek, viz další bod), jsou zvenčí nenarušitelné a dokonale zapouzdřené. To by mělo platit pro všechny objekty v OOP, ale řekněme, že pro entity to platí obzvlášť. Tím nám totiž chrání konzistenci dat.

- Objekty si v OOP až na pár výjimek udržují stav. Entity si svůj stav udržují především v něčem, čemu budeme říkat *položky*. Každý *objekt reálného světa* má určité vlastnosti, které u něj v aplikaci potřebujeme sledovat. Například u autora nás může zajímat jeho jméno, příjmení, webové stránky, u knihy její název, počet kusů na skladě a dalo by se dlouze pokračovat. A právě tyto informace uchováváme v položkách. Každá položka má svůj název, typ a běžně i řadu dalších pravidel pro práci s ní.

- Jejich stav je persistentní. V době, kdy váš PHP skript už dávno skončil, stav vašich entit nadále přežívá uložen v databázi (nebo jiném úložišti) a jakmile je zapotřebí, je z databáze opět načten do operační paměti. *Persistovat entitu* v terminologii Lean Mapperu znamená uložit její aktuální stav do úložiště.

- Mohou plně využívat potenciálu OOP. Kromě stavu v položkách mohou samozřejmě obsahovat i metody, které jej mění, a je-li k tomu důvod, mohou i posílat zprávy jiným objektům. To, co by entita *ještě měla* a *už neměla* dělat, je nad rámec quick startu a budeme se tím hlouběji zabývat v podrobné dokumentaci.

## Definice položek {#toc-polozky}

Položky entity lze nadefinovat dvěma způsoby: pomocí *anotací* a pomocí *přístupových metod*.

### Definice pomocí anotací {#toc-definice-pomoci-anotaci}

Preferovaným (a nejstručnějším) způsobem, jak nadefinovat položky entity, je pomocí anotací. Tímto způsobem lze běžně nadefinovat naprostou většinu položek i vazeb. Pomocí přístupových metod má obvykle smysl definovat pouze takové položky, které jsou natolik specifické, že bychom si u nich s anotacemi z důvodu jejich omezené vyjadřovací schopnosti nevystačili.

Možnosti anotací nejsnáze popíšeme ukázkou:

``` php
<?php

namespace Model\Entity;

use DateTime as PlaceInTime;

/**
 * @property int $id
 * @property PlaceInTime $published
 * @property Author $author m:hasOne
 * @property Author|null $reviewer m:hasOne(reviewer_id)
 * @property Tag[] $tags m:hasMany
 * @property string $title  Name of the book
 * @property-read bool $available = false
 */
class Book extends \LeanMapper\Entity
{
}
```

Položky se definují pomocí PHPDoc anotací `property` a `property-read` (položka pouze pro čtení). Popišme si položky entity `Book`:

```
@property int $id
```

Položka s názvem `id`, která bude obsahovat celočíselnou hodnotu. K její hodnotě půjde přistupovat pomocí `$book->id` nebo voláním `$book->getId()` (API entit se budeme podrobněji věnovat [později](#toc-pristup-k-polozkam)).

```
@property PlaceInTime $published
```

Položka s názvem `published`, která bude obsahovat instanci `DateTime` (viz `use` ještě před PHPDoc komentářem).

```
@property Author $author m:hasOne
```

Položka s názvem `author`, která bude obsahovat instanci `Author`. Pomocí tzv. příznaku `m:hasOne` definujeme 1:N vazbu mezi entitami `Author` a `Book`. Příznakům se budeme podrobněji věnovat [později](#toc-vazby-v-anotacich).

> Příznaky jsou textové fragmenty ve formátu `m:<name>[(<parameters>)]`, které se v anotacích používají pro nějaké speciální účely. Lean Mapper interně rozumí celkem osmi druhům příznaků (`hasOne`, `hasMany`, `belongsToOne`, `belongsToMany`, `useMethods`, `filter`, `passThru` a `enum`), jejichž význam si podrobně popíšeme v relevantních částech dokumentace, plus Lean Mapper umožňuje definici vlastních příznaků pro vaše vlastní, specifické účely.

```
@property Author|null $reviewer m:hasOne(reviewer_id)
```

Položka s názvem `reviewer`, která bude obsahovat instanci `Author` nebo `null`. Opět jsme použili příznak `m:hasOne` pro definici 1:N vazby, jen jsme navíc u této položky upřesnili, jaký sloupec v relaci tento vztah realizuje.

U této položky jsme poprvé použili sufix `|null` u definice typu, který říká, že položka může obsahovat také `null` (je „nullable“). Lean Mapper samozřejmě rozumí i sufixu `|NULL` a stejná vlastnost může být zapsána i pomocí prefixu `null|`, takže zafunguje i `null|Author`. Můžete zkrátka použít takový styl, na jaký jste v PHP zvyklí.

```
@property Tag[] $tags m:hasMany
```

Položka s názem `tags` je zajímavá dvěmi věcmi: sufixem `[]` v definici typu, který říká, že položka obsahuje pole instancí `Tag` (anebo volitelně nějakou kolekci instancí `Tag`) instancí `Tag`, a také je zajímavá příznakem `m:hasMany`, který definuje M:N vazbu mezi entitami `Book` a `Tag`.

```
@property string $title  Name of the book
```

Položka s názvem `title` bude obsahovat textovou hodnotu. Dovětek `Name of the book` je prostý komentář, nemá žádný speciální význam. Obecně jakýkoliv text, který se nachází za definicí názvu položky (respektive za výchozí hodnotou, je-li přítomná, viz dále) a není příznakem (tj. nezačíná prefixem `m:`) plní funkci komentáře. Komentář se může nacházet i mezi příznaky. Neplatí, že komentářem musí definice položky končit.

```
@property-read bool $available = false
```

Položka s názvem `available` bude obsahovat logickou hodnotu a je zajímavá tím, že má nadefinovanou výchozí hodnotu `false`. Takto přirozeně lze v Lean Mapperu nadefinovat výchozí hodnotu nějaké položky. Podrobněji se tomuto tématu budeme věnovat [později](#toc-vychozi-hodnoty).

-----

O tom, jak se definují položky pomocí anotací, jsme si udělali velmi dobrou představu. Na závěr si už jen ukažme pár **nesprávných** definic:

``` php
<?php

namespace Model\Entity;

/**
 * @property int[] $id     Takováto definice kolekcí základního typu není podporována, použijte array
 * @property Author m:hasOne $author     Nesprávné umístění příznaku (musí být až za názvem položky a případnou výchozí hodnotou)
 * @property DateTime $published = new DateTime     Podporovány jsou pouze jednoduché výchozí hodnoty (pro jiné použijte metodu Entity::initDefaults)
 * @property $author     Chybí definice typu
 * @property Author|string $reviewer     Nesprávná definice typu, operátor | lze použít pouze v kombinaci s NULL
 * @property Tag[] $tags m:hasMany m:hasOne     Nesprávné použití příznaků, nedává smysl
 */
class Book extends \LeanMapper\Entity
{
}
```

### Definice pomocí přístupových metod {#toc-definice-pomoci-metod}

Výše uvedenou entitu můžeme kompletně přepsat do následující podoby:

``` php
<?php

namespace Model\Entity;

use DateTime as PlaceInTime;

/**
 * @property Author $author m:hasOne
 * @property Author|null $reviewer m:hasOne(reviewer_id)
 */
class Book extends \LeanMapper\Entity
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

    public function getAuthor()
    {
        $row = $this->row->referenced('author');
        return new Author($row) : null;
    }

    public function setAuthor(Author $author)
    {
        $this->row->author_id = $author->id;
        $this->row->cleanReferencedRowsCache('author', 'author_id');
    }

    public function getReviewer()
    {
        $row = $this->row->referenced('author', 'reviewer_id');
        return $row !== null ? new Author($row) : null;
    }

    public function setReviewer(Author $reviewer = null)
    {
        $this->row->reviewer_id = $reviewer !== null ? $reviewer->id : null;
        $this->row->cleanReferencedRowsCache('author', 'reviewer_id');
    }

    public function getTags()
    {
        $value = array();
        foreach ($this->row->referencing('book_tag') as $row) {
            $value[] = new Tag($row->referenced('tag'));
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

    public function getAvailable()
    {
        return (bool) $this->row->available;
    }

    public function setAvailable($available)
    {
        $this->row->available = (bool) $available;
    }
}
```

Jedná se o transparentní, „nemagickou“ variantu, avšak patřičně upovídanou. Na druhou stranu, vyjadřovací schopnosti metod jsou větší než anotací, a proto občas není jiného východiska, než metody použít. Oba způsoby definic položek lze samozřejmě kombinovat, dokonce i v rámci jedné entity.

Všimněte si, že ve všech předvedených metodách se přistupuje k protected proměnné `$row`. Entity v Lean Mapperu totiž vlastní data neudržují. Udržují jen instanci třídy `LeanMapper\Row`, která vlastní data zapouzdřuje.

Toto zdánlivě kostrbaté řešené umožňuje pokládat efektivní dotazy do databáze (ve stylu „NotORM“). Při typickém používání vás ale zmíněné interní záležitosti Lean Mapperu vůbec nemusí trápit. Seznamte se jen s veřejným rozhraním třídy `LeanMapper\Row`, abyste měli představu, co všechno se s jejími instancemi v entitách dá dělat.


### Priorita definic {#toc-priorita-definic}

Pokud je definována přístupová metoda, má při přístupu k položce vždy přednost před anotací.

**Poznámka:** *před verzí 2.0 fungovala priorita definic odlišným způsobem.*


## Rozhraní entity {#toc-rozhrani-entity}

### Přístup k položkám {#toc-pristup-k-polozkam}

Následující ukázka demonstruje, jak lze k položkám entity přistupovat. Je jedno zda jsou položky definovány pomocí anotací nebo pomocí přístupových metod:

``` php
<?php
// $book instanceof Model\Entity\Book
$book->id;
$book->title;
$book->getAvailable();
$book->title = 'New title';
$book->setTitle('New title');
```


### Hromadné přiřazování {#toc-hromadne-prirazovani}

Užitečnou metodou je metoda `assign(array $values, array $whitelist = null)`, kterou entity dědí z `LeanMapper\Entity`. Umožňuje hromadně přiřadit hodnoty do více položek:

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


## Definice vazeb (vztahů mezi entitami) a strategie {#toc-vazby-v-anotacich}

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


## Příznaky m:passThru, m:enum {#toc-priznaky-passthru-enum}


## Správa jednoduchých M:N vazeb {#toc-sprava-mn-vazeb}

Jednoduchá M:N vazba je taková vazba, která je v relační databázi reprezentována spojovací tabulkou, která neobsahuje nic jiného, než odkazy do spojovaných tabulek a volitelně umělý primární klíč. Zpravidla má tedy taková tabulka dva, nebo tři sloupce.

***Tip:*** *Pokud potřebujete v Lean Mapperu pracovat se spojovací tabulkou, která pro každou vazbu obsahuje i nějaké další doplňující informace (zda je vazba aktivní, datum jejího vzniku,…), best practice je mít pro takovou tabulku samostatnou entitu (a podle potřeby i repositář).*

Současné API vypadá následovně:


``` php?start_inline=1
// Nějaká úvodní kaše
class Mapper extends LeanMapper\DefaultMapper
{

    protected $defaultEntityNamespace = null;

    public function getPrimaryKey($table)
    {
        if ($table === 'tag') {
            return 'code';
        }
        return parent::getPrimaryKey($table);
    }

}

/**
 * @property string $code
 * @property string $name
 */
class Tag extends LeanMapper\Entity
{
}

/**
 * @property int $id
 * @property Tag[] $tags m:hasMany
 * @property DateTime|null $released
 * @property string $title
 * @property string|null $web
 * @property string $slogan
 */
class Application extends LeanMapper\Entity
{
}
```

``` php?start_inline=1
$mapper = new Mapper;
$applicationRepository = new ApplicationRepository($connection, $mapper);
$tagRepository = new TagRepository($connection, $mapper);

$application = $applicationRepository->find(1);

$tag = $tagRepository->find('JavaScript'); // this is our primary key...

$application->addToTags($tag);

$application->removeFromTags($tag);

$application->addToTags('JavaScript');

$application->removeFromTags('JavaScript');

$application->removeAllTags();
$application->replaceAllTags(array($tag1, $tag2)); // mass replace by Entity[]
$application->replaceAllTags(array(5, 6)); // mass replace by IDs

$applicationRepository->persist($application);
```

Jak vidno, tagy lze přidávat a odebírat buďto na základě instance Tag, anebo na základě ID tagu (v našem příkladu kódu).

Co je asi samozřejmé je, že pokud přistoupíte i před persistencí ke kolekci `$tags` (například tagy vypíšete), přidané a odebrané tagy se již projeví. V paměti tyto změny provedené už jsou, jen ještě nejsou persistované v databázi.

Lean Mapper si při persistenci těchto vazeb inteligentně hlídá, co je zapotřebí v databázi přidat a odstranit a stav sesynchronizuje velmi malým počtem dotazů:

1. Jedním multi-insertem hromadně vkládajícím potřebné nové vazby
2. Voláním DELETE pro jednotlivé vazby s tím, že pokud například aplikace má tři vazby na tag JavaScript a dvě se mají odstranit, využije se ```LIMIT (DELETE ... WHERE `code` = 'JavaScript' LIMIT 2)```.

Také platí, že pokud například desetkrát odeberete vazbu na tag `SQL`, který ale aplikace nemá ani jednou přiřazený, žádné dotazy se negenerují.

**POZOR!** Aby vše takhle hezky mohlo fungovat, existuje jedno drobné omezení. Pokud vytváříte například novou entitu aplikace `(new Application)`, je zapotřebí ji před tím, než jí začnete přiřazovat tagy, **persistovat**.

``` php?start_inline=1
$application = new Application(array(
    'title' => 'New application',
    'slogan' => 'lorem ipsum',
));

$applicationRepository->persist($application);

$application->addToTags('PHP');
$application->addToTags('JavaScript');

$applicationRepository->persist($application);
```

Při úpravě již existují entity načtené z repositáře takovéto persistování nadvakrát samozřejmě zapotřebí není:

``` php?start_inline=1
$application = $applicationRepository->find(1);

$application->title = 'New title';

$application->addToTags('PHP');
$application->addToTags('JavaScript');

$applicationRepository->persist($application);
```


## Defaultní (výchozí) hodnoty {#toc-vychozi-hodnoty}


## Vlastní příznaky {#toc-vlastni-priznaky}


[« Úvod do dokumentace](/cs/docs/) | [Repositáře »](/cs/docs/repositare/)
