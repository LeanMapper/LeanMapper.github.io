---
title: EntityFactory
rank: 70
---

EntityFactory je třída implementující rozhraní `LeanMapper\IEntityFactory`. Stará se o vytváření entit.

Výchozí implementací je třída `LeanMapper\DefaultEntityFactory`.

**Poznámka:** EntityFactory je dostupná až od verze [2.1.0](/cs/changelog/).


## Metody

### createEntity($entityClass, $arg)

Metoda má za úkol vytvořit instanci entity. K tomu obdrží tyto parametry:

* `$entityClass` - celý název třídy entity (např. `Model\Entity\Author`)
* `$arg` - data entity buď jako objekt `LeanMapper\Row`, objekt implementující rozhraní `Traversable`, pole (`array`), nebo `NULL`

Vrací potomka třídy `LeanMapper\Entity`.

***Poznámka:** pokud chcete do entity injectovat nějaké závislosti, můžete to udělat právě v této metodě.*


### createCollection($entities)

Metoda obdrží pole objektů `LeanMapper\Entity` a z nich má za úkol vyrobit kolekci. Výchozí implementace vrací kolekci ve formě pole (`array`).


[« Mapper](/cs/docs/mapper/) | [Integrace Lean Mapperu do aplikace »](/cs/docs/integrace-do-aplikace/)
