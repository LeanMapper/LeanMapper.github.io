---
title: Mapper
rank: 60
---

Mapper je třída implementující rozhraní `LeanMapper\IMapper`. Mapper definuje výchozí [konvence](../konvence/) používané systémem. Jedná se např. o tvar názvu vazební tabulky, převod názvu entity na název tabulky, apod.

Výchozí implementací je třída `LeanMapper\DefaultMapper`.

**Poznámka:** Mapper je dostupný až od verze [2.0.0](/cs/changelog/). Ve starších verzích sloužily k částečnému ovlivnění konvencí metody `LeanMapper\Entity::getEntityClass` a `LeanMapper\Repository::getEntityClass`.


## Související

* Příklady / [CamelCase to under_score mapper](/cs/tutorials/camelcase-to-underscore-mapper/)


[« Filtry](/cs/docs/filtry/) | [EntityFactory »](/cs/docs/entity-factory/)
