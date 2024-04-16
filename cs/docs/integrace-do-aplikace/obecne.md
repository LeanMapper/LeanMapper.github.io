---
title: Obecně
rank: 10
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
$connection = new LeanMapper\Connection([
    'driver'   => 'mysqli',
    'host'     => 'localhost',
    'username' => 'root',
    'password' => '***',
    'database' => 'mydatabase',
]);
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
