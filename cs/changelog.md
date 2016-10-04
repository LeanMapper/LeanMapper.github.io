---
title: Changelog
---


## [2.0.1](https://github.com/Tharos/LeanMapper/tree/v2.0.1) (12. 9. 2013)

* Přidány metody `LeanMapper\Result::cleanReferencingResultsCache` a `LeanMapper\Row::cleanReferencingRowsCache`.

	[Informace na GitHubu](https://github.com/Tharos/LeanMapper/issues/10)


## [2.0.0](https://github.com/Tharos/LeanMapper/tree/v2.0.0) (26. 8. 2013)

* Přidána podpora pro vlastní konvence – rozhraní `LeanMapper\IMapper` a defaultní implementace `LeanMapper\DefaultMapper` (BC break)

	[Informace na Nette fóru](http://forum.nette.org/cs/14592-lean-mapper-tenke-orm-nad-dibi?p=4#p105850)

	[Ukázka hezkého mapperu od Jana Nedbala](http://pastebin.com/dZjk1qaw)

* Odstraněna přežitá metoda `LeanMapper\Entity::getEntityClass`, je žádoucí použít `LeanMapper\IMapper::getEntityClass` (BC break)

	[Informace na Nette fóru](http://forum.nette.org/cs/14592-lean-mapper-tenke-orm-nad-dibi?p=9#p108540)

* Odstraněna přežitá metoda `LeanMapper\Repository::getEntityClass`, je žádoucí použít `LeanMapper\IMapper::getEntityClass` (BC break)

	[Informace na Nette fóru](http://forum.nette.org/cs/14592-lean-mapper-tenke-orm-nad-dibi?p=9#p108540)

* Přidána protected metoda `LeanMapper\Entity::initDefaults`

	[Informace na Nette fóru](http://forum.nette.org/cs/14592-lean-mapper-tenke-orm-nad-dibi?p=4#p105770)

* Přidána podpora pro výchozí hodnoty uvedené v anotacích

	[Informace na Nette fóru](http://forum.nette.org/cs/14592-lean-mapper-tenke-orm-nad-dibi?p=9#p108616)

* Přidána podpora pro správu jednoduchých M:N vazeb

	[Informace na Nette fóru](http://forum.nette.org/cs/14592-lean-mapper-tenke-orm-nad-dibi?p=4#p105942)

* Přidána podpora pro single table inehritance

	[Informace na Nette fóru](http://forum.nette.org/cs/14592-lean-mapper-tenke-orm-nad-dibi?p=5#p106593)

* Vylepšena podpora výčtového typu (přidána metoda `LeanMapper\Reflection\Property::getEnumValues`)

	[Informace na Nette fóru](http://forum.nette.org/cs/14592-lean-mapper-tenke-orm-nad-dibi?p=6#p107052)

* Zlepšen parser anotací, přidány nové příznaky a odstraněn příznak `m:extra` (BC break)

	[Informace na Nette fóru](http://forum.nette.org/cs/14592-lean-mapper-tenke-orm-nad-dibi?p=8#p107950)

* Přidán whitelist do metody `LeanMapper\Entity::getData`

	[Informace na Nette fóru](http://forum.nette.org/cs/14592-lean-mapper-tenke-orm-nad-dibi?p=8#p107950)

* Přepracován systém filtrů – nové třídy `LeanMapper\Connection` a `LeanMapper\Fluent` (BC break)

	[Informace na Nette fóru](http://forum.nette.org/cs/14592-lean-mapper-tenke-orm-nad-dibi?p=9#p108425) \\
	[Informace na Nette fóru](http://forum.nette.org/cs/14592-lean-mapper-tenke-orm-nad-dibi?p=9#p108493)

* Přidán systém událostí

	[Informace na Nette fóru](http://forum.nette.org/cs/14592-lean-mapper-tenke-orm-nad-dibi?p=10#p108822) \\
	[Propojení s Kdyby\Events](http://forum.nette.org/cs/15165-observer-v-nette-mam-spravny-navrh#p108884)

* Provedena dekompozice `LeanMapper\Repository` (vyčleněny protected metody `Repository::insertIntoDatabase`, `Repository::updateInDatabase`, `Repository::deleteFromDatabase`)

	[Informace na Nette fóru](http://forum.nette.org/cs/14592-lean-mapper-tenke-orm-nad-dibi?p=11#p109028)

* Přejmenovány metody `markAsCreated` na `markAsAttached` (BC break)

	[Informace na Nette fóru](http://forum.nette.org/cs/14592-lean-mapper-tenke-orm-nad-dibi?p=11#p109108)

* Přidána metoda `LeanMapper\Entity::__isset`

	[Informace na Nette fóru](http://forum.nette.org/cs/14592-lean-mapper-tenke-orm-nad-dibi?p=11#p109211)

* Entity není možné vytvářet z detached `LeanMapper\Row` (BC break)

* Změněna viditelnost několika metod (všechny na méně omezující variantu)

* Zlepšen výkon jádra a in-memory cache

* Vylepšeny chybové hlášky

* Při přístupu k položce mají metody vždy přednost před anotacemi

	[Informace na Nette fóru](http://forum.nette.org/cs/14592-lean-mapper-tenke-orm-nad-dibi?p=8#p107950)


## [1.4.0](https://github.com/Tharos/LeanMapper/tree/v1.4.0) (21. 6. 2013)

* V `LeanMapper\Result` se negeneruje `SELECT *`, ale `SELECT table.*` (teoreticky je to BC break)

* Statická proměnná `LeanMapper\Repository::$defaultEntityNamespace` byla nahrazena stejnojmennou protected proměnnou (BC break)

* Hodnota anotace `@entity` nad repositářem může být fully qualified (začíná `\`) a pokud není, tak se využívá `$defaultEntityNamespace` (BC break)

* Nízkoúrovňová metoda `getModifiedData()` byla přejmenována na `getModifiedRowData()`, byla doplněna nízkoúrovňová `getRowData()` a vysokoúrovňová `getData()` (BC break)

* Konstruktor entity nově umí přijmout i pole nebo instanci `Traversable`

* Do `Repository` a `Entity` byla doplněna protected metoda `createCollection()`, jejímž přetížením lze zařídit, aby Lean Mapper vracel skupinu entit v nějaké uživatelské kolekci namísto jednoduchého `array`

* Upraven `AliasesParser` tak, že stavovým automatem prochází jenom ty části kódu, ve kterých může být nějaký `use` (významné zlepšení výkonu)

* Upravena kontrola typů položek tak, že jsou přijímány i potomci vyžadovaných tříd (pokud položka vyžaduje `DateTime`, nově prochází i `DibiDateTime` atp.)

* Přidán příznak `m:extra` (možnost snadného vlastního rozšíření anotace)

* Upraven `LeanMapper\Result` tak, že při získávání souvisejících dat může volitelně využívat „IN“ nebo „UNION“ strategii (viz jak to řeší NotORM a také viz [tento článek](http://www.xaprb.com/blog/2006/12/07/how-to-select-the-firstleastmax-row-per-group-in-sql/)) – je to důležité pro správné limitování a řazení

* Přidán příznak `m:enum` (podpora pro výčtový typ, například `m:enum(self::STATUS_*))` – thx [@JanTvrdik](http://forum.nette.org/cs/14592-lean-mapper-tenke-orm-nad-dibi#p105080)



## [1.3.1](https://github.com/Tharos/LeanMapper/tree/v1.3.1) (10. 6. 2013)

* Přidána anotace `@property-read` pro definici *read only* položek entity

* V anotacích `@property` a `@property-read` v entitě je nově možné upřesnit, na jaký sloupec v `LeanMapper\Row` se položka mapuje (např. `@property string $bookName (book_name)`)

* Opraveny známé chyby
