---
title: Integrace Lean Mapperu do aplikace
rank: 80
---

* [Úvod](#page-title)
* [Vytvoření připojení](#toc-pripojeni)
* [Mapper](#toc-mapper)
* [EntityFactory](#toc-entityfactory)
* [Repositáře](#toc-repositare)
* [Integrace do Nette](#toc-nette)
    * [DI rozšíření](#toc-nette-extension)
    * [Ruční definice](#toc-nette-config)
    * [Předání repositáře do presenteru](#toc-nette-presenter)


## Vytvoření připojení  {#toc-pripojeni}

Vytvoříme si objekt `LeanMapper\Connection` a předáme mu parametry potřebné pro připojení k databázi.

``` php?start_inline=1
$connection = new LeanMapper\Connection(array(
    'driver'   => 'mysqli',
    'host'     => 'localhost',
    'username' => 'root',
    'password' => '***',
    'database' => 'mydatabase',
));
```

**Tip:** `LeanMapper\Connection` přebírá stejné parametry jako třída [`Dibi\Connection`](https://api.dibiphp.com/Dibi.Connection.html).


## Mapper  {#toc-mapper}

Dále potřebujeme objekt implementující [`LeanMapper\IMapper`](/cs/docs/mapper/). Lean Mapper nám poskytuje výchozí implementaci formou třídy `LeanMapper\DefaultMapper`.

``` php?start_inline=1
$mapper = new LeanMapper\DefaultMapper;
```


## Entity factory  {#toc-entityfactory}

A jako poslední věc vytvoříme objekt, který implementuje rozhraní [`LeanMapper\IEntityFactory`](/cs/docs/entity-factory). Lean Mapper nám opět podává pomocnou ruku prostřednictvím třídy `LeanMapper\DefaultEntityFactory`.

``` php?start_inline=1
$entityFactory = new LeanMapper\DefaultEntityFactory;
```


## Repositáře  {#toc-repositare}

Abychom mohli pracovat s [entitami](/cs/docs/entity/), potřebujeme k tomu [repositáře](/cs/docs/repositare/). Předpokládejme, že máme repositář napsaný, nyní ho jen vytvoříme a předáme mu potřebné závislosti.

``` php?start_inline=1
$bookRepository = new Model\BookRepository($connection, $mapper, $entityFactory);
```

A to je vše.


## Integrace do Nette aplikace  {#toc-nette}

Ještě si ukážeme, jak použít Lean Mapper v rámci aplikace napsané v [Nette](https://nette.org).

### DI rozšíření  {#toc-nette-extension}

Doporučenou cestou je použití DI rozšíření. Do konfiguračního souboru aplikace (`config.neon`) si přidáme následující definici:

``` yaml
extensions:
    leanmapper: LeanMapper\Bridges\Nette\DI\LeanMapperExtension

leanmapper:
    db:
        driver: mysqli
        host: localhost
        username: ...
        password: ...
        database: mydatabase

services:
    - Model\BookRepository
```

**Poznámka:** *rozšíření je dostupné od verze **3.0**.*

*Pokud vám výchozí DI rozšíření nevyhovuje, můžete zkusit [doplňky](/cs/rozsireni/#integrace-do-nette) vyvíjené komunitou.*


### Ruční definice  {#toc-nette-config}

Pokud nechceme, nebo nemůžeme použít předpřipravené DI rozšíření, můžeme jednotlivé *služby* definovat ručně. Do konfiguračního souboru aplikace (`config.neon`) si přídáme následující parametry a definice služeb:

``` yaml
parameters:
    # údaje pro připojení k DB
    leanmapper:
        driver: mysqli
        host: localhost
        username: ...
        password: ...
        database: mydatabase

services:
    # registrace Lean Mapperu
    - LeanMapper\Connection(%leanmapper%)
    - LeanMapper\DefaultMapper
    - LeanMapper\DefaultEntityFactory

    # registrace repositářů
    - Model\AuthorRepository
    - Model\BookRepository
```

Repositářům nemusíme ručně předávat závislosti, o to se automaticky postará [auto-wiring](https://doc.nette.org/cs/2.4/configuring#toc-auto-wiring) v Nette.


### Předání repositáře do presenteru  {#toc-nette-presenter}

Preferovaným způsobem je předání závislostí přes konstruktor.

``` php?start_inline=1
class BookPresenter extends BasePresenter {
    /** @var \Model\BookRepository */
    private $bookRepository;

    public function __construct(\Model\BookRepository $bookRepository)
    {
        $this->bookRepository = $bookRepository;
    }
}
```


Alternativně můžeme použít anotaci [`@inject`](https://doc.nette.org/cs/2.4/presenters#toc-pouziti-modelovych-trid).

``` php?start_inline=1
class BookPresenter extends BasePresenter {
    /** @var \Model\BookRepository @inject */
    public $bookRepository;

    ...
}
```

V rámci presenteru pak máme repositář přístupný přes `$this->bookRepository`.

[« Mapper](/cs/docs/mapper/) | [SQL strategie »](/cs/docs/sql-strategie/)
