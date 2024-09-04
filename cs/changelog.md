---
title: Changelog
redirect_from: "/changelog"
rank: 70
---

## [Vývojová verze](https://github.com/Tharos/LeanMapper/tree/develop)


## [4.1.0](https://github.com/Tharos/LeanMapper/tree/v4.1.0) (4. 9. 2024)

* Entity: přidána traita `Initialize` ([#167](https://github.com/Tharos/LeanMapper/pull/167))

* Entity: přidána podpora pro typ `non-empty-string` u položek entity ([#167](https://github.com/Tharos/LeanMapper/pull/167))

* Entity: `settype()` nahrazeno vlastní metodou `Helpers::convertType()` ([#167](https://github.com/Tharos/LeanMapper/pull/167))

* Entity: opraveno pořadí volání kontroly typu a setter pass ([#167](https://github.com/Tharos/LeanMapper/pull/167))

* Entity: přidána podpora pro nullable syntaxi `?Foo` ([#167](https://github.com/Tharos/LeanMapper/pull/167))

* IEntityFactory: vylepšena návratová hodnota u metody `createCollection()` ([#167](https://github.com/Tharos/LeanMapper/pull/167))


## [4.0.5](https://github.com/Tharos/LeanMapper/tree/v4.0.3) (28. 11. 2023)

* přidána podpora pro PHP 8.3 ([#165](https://github.com/Tharos/LeanMapper/pull/165))


## [4.0.4](https://github.com/Tharos/LeanMapper/tree/v4.0.3) (15. 8. 2023)

* přidána podpora pro Dibi 5.x ([#164](https://github.com/Tharos/LeanMapper/pull/164))


## [4.0.3](https://github.com/Tharos/LeanMapper/tree/v4.0.3) (16. 2. 2022)

* opravena kompatibilita s PHP 8.1 ([#163](https://github.com/Tharos/LeanMapper/pull/163))


## [4.0.2](https://github.com/Tharos/LeanMapper/tree/v4.0.2) (18. 1. 2022)

* Entita: opravena chyba při čtení nullable položky s vazbou `m:hasOne` ([#161](https://github.com/Tharos/LeanMapper/issues/161))


## [4.0.1](https://github.com/Tharos/LeanMapper/tree/v4.0.1) (20. 10. 2021)

* EntityReflection: opravena chyba při použití FQN (`\Foo\Bar`) v příznaku `m:enum`


## [4.0.0](https://github.com/Tharos/LeanMapper/tree/v4.0.0) (25. 3. 2021)

* Entity: přiřazení hodnoty (`set()`/`__set()`) kontroluje typ položky, tj. nejde do položky typu `int` přiřadit `string` apod. (BC BREAK)

* Result: opravena chyba při volání `cleanReferencing`/`cleanReferenced` v kombinaci s `FilteringResult`

* používá typehinty dostupné od PHP 7.1 (BC BREAK)

* Repository: vyhazuje vyjímku při přístupu k neexistující property (např. `$repository->onUnexists[]`)

* Fluent: nepřepisuje statickou proměnnou `$masks` (BC BREAK)

* úpravy v kódu, coding style

* přidána možnost v mapperu konvertovat hodnoty načtené z databáze před předáním do `LeanMapper\Row` a zpět (umožňuje např. používat value objekty bez anotace `m:passThru` v entitě)

* kód je testován pomocí PhpStanu

* Nette DI extension: opravena kompatibilita s nette/di ^3.0

* všechny soubory obsahují `declare(strict_types=1)`

* IMapper: `getRelationshipColumn()` - přidán nový parametr `$relationshipName` ([#77](https://github.com/Tharos/LeanMapper/issues/77))

* DefaultMapper: `$defaultEntityNamespace` se nově mění pomocí konstruktoru (BC BREAK)

* Vyžaduje Dibi 4.x a PHP 7.2 nebo novější (BC break)


## [3.4.2](https://github.com/Tharos/LeanMapper/tree/v3.4.2) (1. 4. 2020)

* Nette DI extension: opravena kompatibilita s nette/robot-loader 3.0+ ([#151](https://github.com/Tharos/LeanMapper/pull/151))


## [3.4.1](https://github.com/Tharos/LeanMapper/tree/v3.4.1) (14. 5. 2019)

* Repository: opraveno použití parametru `$table` v metodě `createEntities()` ([#148](https://github.com/Tharos/LeanMapper/pull/148), [4f4f9bf](https://github.com/Tharos/LeanMapper/commit/4f4f9bf1eaec31b295219406550f5ddad28381cd))

* opraven coding style ([#147](https://github.com/Tharos/LeanMapper/pull/147), [6bcd65a](https://github.com/Tharos/LeanMapper/commit/6bcd65add0fcf51b6c6189bd3ff781e5121b9ecb))


## [3.4.0](https://github.com/Tharos/LeanMapper/tree/v3.4.0) (16. 3. 2019)

* Nette DI extension: file logger používal neexistující třídu, nyní používá `Dibi\Loggers\FileLogger` ([#145](https://github.com/Tharos/LeanMapper/pull/145))

* EntityReflection: přidána možnost přizpůsobit údaje poskytované reflexí pomocí `IEntityReflectionProvider` ([#141](https://github.com/Tharos/LeanMapper/pull/141))

* SQLite3 - volání `$entity->removeFromX()` způsobilo chybu, pokud nebylo SQLite zkompilováno s volbou `SQLITE_ENABLE_UPDATE_DELETE_LIMIT` ([#143](https://github.com/Tharos/LeanMapper/pull/143))

* Result: `addToReferencing` ignoruje duplicitní hodnoty ([#143](https://github.com/Tharos/LeanMapper/pull/143))

* Result: vytvoření instance pomocí `new self` změněno na `new static` ([#140](https://github.com/Tharos/LeanMapper/pull/140))

* Přidána podpora pro implicitní passThru ([#137](https://github.com/Tharos/LeanMapper/pull/137))

* EntityReflection: vylepšen výkon ([#132](https://github.com/Tharos/LeanMapper/pull/132))


## [3.3.0](https://github.com/Tharos/LeanMapper/tree/v3.3.0) (11. 8. 2018)

* Změněn výchozí sloupec u `hasOne` vazeb, `DefaultMapper` v názvu sloupce použije název položky místo názvu tabulky (BC break, [#77](https://github.com/Tharos/LeanMapper/pull/77), [#127](https://github.com/Tharos/LeanMapper/issues/127))

* Vazby `belongsTo` jsou označeny jako pouze pro čtení ([#124](https://github.com/Tharos/LeanMapper/pull/124), [#62](https://github.com/Tharos/LeanMapper/issues/62))

* Přidána podpora pro `m:hasMany(#inversed)` ([#125](https://github.com/Tharos/LeanMapper/pull/125), [#123](https://github.com/Tharos/LeanMapper/issues/123))

* Přidána podpora pro víceřádkové anotace ([#108](https://github.com/Tharos/LeanMapper/pull/122), [#29](https://github.com/Tharos/LeanMapper/issues/29))


## [3.2.0](https://github.com/Tharos/LeanMapper/tree/v3.2.0) (1. 5. 2018)

* Hodnota příznaku v anotacích může nyní obsahovat zanořené závorky (např. `m:default(array())`) ([#122](https://github.com/Tharos/LeanMapper/pull/122))

* V anotaci položky nelze pro zápis výchozí hodnoty použít rovnítkovou syntaxi zároveň s příznakem `m:default` ([#122](https://github.com/Tharos/LeanMapper/pull/122))

* Hodnota zapsaná v příznaku `m:default` je konvertována na správný datový typ ([#122](https://github.com/Tharos/LeanMapper/pull/122))

* Opravena chyba, kdy příznak `m:default` nebyl označen jako výchozí hodnota (`$property->hasDefaultValue()` vracelo `false`) ([#119](https://github.com/Tharos/LeanMapper/pull/119))

* Opravena chyba se změnou datového typu v passThru setteru ([#117](https://github.com/Tharos/LeanMapper/pull/117), [#118](https://github.com/Tharos/LeanMapper/pull/118))

* Opraveno generování složitějších SQL dotazů při použití UNION strategie ([#109](https://github.com/Tharos/LeanMapper/pull/109))

* Opravena chyba, kdy se Lean Mapper snažil použít i settery a gettery s viditelností private a protected ([#97](https://github.com/Tharos/LeanMapper/pull/97))

* Opravena chyba, kdy při použití `m:enum` nešlo do nullable položky přiřadit `null` ([#116](https://github.com/Tharos/LeanMapper/pull/116))

* PostgreSQL - opravena [chyba](https://github.com/Tharos/LeanMapper/issues/59) při volání `$entity->removeFromX()` ([#114](https://github.com/Tharos/LeanMapper/pull/114))

* Vylepšena detekce položek s duplicitním názvem ([#104](https://github.com/Tharos/LeanMapper/pull/104))

* Přidána podpora pro pomlčku v názvech příznaků (např. `m:flag-name`) ([#107](https://github.com/Tharos/LeanMapper/pull/107))

* `m:enum` umožňuje použít neprefixované konstanty (`SomeClass::*`) ([#100](https://github.com/Tharos/LeanMapper/pull/100))


## [3.1.1](https://github.com/Tharos/LeanMapper/tree/v3.1.1) (10. 7. 2016)

[Oznámení na GitHubu (anglicky)](https://github.com/Tharos/LeanMapper/releases/tag/v3.1.1)

* Nette DI rozšíření - opravena kompatibilita s Nette 2.4

* Známé chyby:
	[#97](https://github.com/Tharos/LeanMapper/pull/97)

Všechny změny lze vidět v tomto [diffu](https://github.com/Tharos/LeanMapper/compare/v3.1.0...v3.1.1?expand=1).


## [3.1.0](https://github.com/Tharos/LeanMapper/tree/v3.1.0) (9. 5. 2016)

[Oznámení na GitHubu (anglicky)](https://github.com/Tharos/LeanMapper/releases/tag/v3.1.0)

* Opravena chyba [#85](https://github.com/Tharos/LeanMapper/issues/85)

* Vylepšeny chybové hlášky

* `Entity::get` vrací `null` pro nullable položky detachovaných entit

* `Entity::get` a `Entity::set` umožňuje přes `passThru` změnu datového typu (BC break)

* Nette DI rozšíření - opravena chyba [#95](https://github.com/Tharos/LeanMapper/pull/95)

* Vyžaduje PHP 5.4 nebo vyšší (BC break)

* Místo zastaralého balíčku `dg/dibi` vyžaduje `dibi/dibi`

* Známé chyby:
	[#97](https://github.com/Tharos/LeanMapper/pull/97)

Všechny změny lze vidět v tomto [diffu](https://github.com/Tharos/LeanMapper/compare/v3.0.0...v3.1.0?expand=1).


## [3.0.0](https://github.com/Tharos/LeanMapper/tree/v3.0.0) (8. 3. 2016)

[Oznámení na GitHubu (anglicky)](https://github.com/Tharos/LeanMapper/releases/tag/v3.0.0)

* Vylepšen výkon

* Vyžaduje Dibi 3.x (BC break)

* Přidáno rozšíření pro Nette DI (obsahuje chybu [#95](https://github.com/Tharos/LeanMapper/pull/95))

* Přidána anotace `m:default` jako alternativa pro nastavení výchozí hodnoty property

* Přidána anotace `m:column` jako alternativa pro nastavení názvu sloupce

* Známé chyby:
	[#85](https://github.com/Tharos/LeanMapper/issues/85),
	[#95](https://github.com/Tharos/LeanMapper/pull/95),
	[#97](https://github.com/Tharos/LeanMapper/pull/97)

Všechny změny lze vidět v tomto [diffu](https://github.com/Tharos/LeanMapper/compare/v2.3.0...v3.0.0?expand=1).


## [2.3.0](https://github.com/Tharos/LeanMapper/tree/v2.3.0) (9. 2. 2016)

* Přidána podpora pro `ResultProxy`

	[Informace na GitHubu](https://github.com/Tharos/LeanMapper/issues/53#issuecomment-41611844)

* Zjednodušeno a vylepšeno rozhraní entity, kód z magických metod `Entity::__get` a `Entity::__set` přesunut do nových metod `Entity::get` a `Entity::set`

* Změněna viditelnost metody `Entity::mergeFilters` na `protected`

* Vylepšena metoda `Entity::__isset` ([commit](https://github.com/Tharos/LeanMapper/commit/b4a9dc7d99227d68721e4df23e3049d62c0a82dc))

* Vylepšen výkon

* Opravena chyba [#73](https://github.com/Tharos/LeanMapper/issues/73)

* Různá vylepšení a opravy

* Známé chyby:
	[#85](https://github.com/Tharos/LeanMapper/issues/85),
	[#97](https://github.com/Tharos/LeanMapper/pull/97)

Všechny změny lze vidět v tomto [diffu](https://github.com/Tharos/LeanMapper/compare/v2.2.0...v2.3.0?expand=1).


## [2.2.0](https://github.com/Tharos/LeanMapper/tree/v2.2.0) (27. 4. 2014)

[Oznámení na fóru](https://forum.dibiphp.com/cs/14592-lean-mapper-tenke-orm-nad-dibi?p=22#p124335)

* Zachovávání kolekce ID ve Fluent

	[Informace na GitHubu](https://github.com/Tharos/LeanMapper/issues/30)

* Nová metoda  Connection::hasFilter

	[Informace na GitHubu](https://github.com/Tharos/LeanMapper/pull/26)

* Nově se lze odkazovat na aliasy v SQL

	[Informace na fóru](https://forum.dibiphp.com/cs/14592-lean-mapper-tenke-orm-nad-dibi?p=20#p119516)

* [„Preloading“](https://github.com/Tharos/LeanMapper/commit/f21c9f7898633ece4ac30fdc9b73f43824a6d09d), který umožňuje vznik [nadstavby zvané LQL](https://github.com/Tharos/LeanMapper/issues/46)

Všechny změny lze vidět v tomto [diffu](https://github.com/Tharos/LeanMapper/compare/v2.1.0...v2.2.0?expand=1).


## [2.1.0](https://github.com/Tharos/LeanMapper/tree/v2.1.0) (13. 12. 2013)

[Oznámení na fóru](https://forum.dibiphp.com/cs/14592-lean-mapper-tenke-orm-nad-dibi?p=18#p115098)

* Přidáno rozhraní `IEntityFactory` včetně výchozí implementace `DefaultEntityFactory` (BC break)

	[Informace na fóru](https://forum.dibiphp.com/cs/14592-lean-mapper-tenke-orm-nad-dibi?p=15#p113095)

* Zásadní zlepšení chybových hlášek

	[Informace na fóru](https://forum.dibiphp.com/cs/14592-lean-mapper-tenke-orm-nad-dibi?p=15#p113095)

* Implicitní filtry

	[Informace na fóru](https://forum.dibiphp.com/cs/14592-lean-mapper-tenke-orm-nad-dibi?p=16#p113453)

* Anonymní filtry

	[Informace na fóru](https://forum.dibiphp.com/cs/14592-lean-mapper-tenke-orm-nad-dibi?p=16#p114029)

* Dekompozice `Entity::__get` a `Entity::__set`

	[Informace na fóru](https://forum.dibiphp.com/cs/14592-lean-mapper-tenke-orm-nad-dibi?p=17#p114413)

* `Entity::createCollection` a `Repository::createCollection` přesunuto do `IEntityFactory::createCollection` (BC break)

	[Informace na fóru](https://forum.dibiphp.com/cs/14592-lean-mapper-tenke-orm-nad-dibi?p=17#p114393)

* přidána podpora pro výchozí hodnoty (v anotaci) null a prázdný řetězec

	[Informace na fóru](https://forum.dibiphp.com/cs/14592-lean-mapper-tenke-orm-nad-dibi?p=18#p114545)

* z vlastních getterů a setterů se lze nově odkazovat na `__get` a `__set`

	[Informace na fóru](https://forum.dibiphp.com/cs/14592-lean-mapper-tenke-orm-nad-dibi?p=18#p115096)

Všechny změny lze vidět v tomto [diffu](https://github.com/Tharos/LeanMapper/compare/v2.0.1...v2.1.0?expand=1).


## [2.0.1](https://github.com/Tharos/LeanMapper/tree/v2.0.1) (12. 9. 2013)

* Přidány metody `LeanMapper\Result::cleanReferencingResultsCache` a `LeanMapper\Row::cleanReferencingRowsCache`.

	[Informace na GitHubu](https://github.com/Tharos/LeanMapper/issues/10)


## [2.0.0](https://github.com/Tharos/LeanMapper/tree/v2.0.0) (26. 8. 2013)

* Přidána podpora pro vlastní konvence – rozhraní `LeanMapper\IMapper` a defaultní implementace `LeanMapper\DefaultMapper` (BC break)

	[Informace na fóru](https://forum.dibiphp.com/cs/14592-lean-mapper-tenke-orm-nad-dibi?p=4#p105850)

	[Ukázka hezkého mapperu od Jana Nedbala](https://pastebin.com/dZjk1qaw)

* Odstraněna přežitá metoda `LeanMapper\Entity::getEntityClass`, je žádoucí použít `LeanMapper\IMapper::getEntityClass` (BC break)

	[Informace na fóru](https://forum.dibiphp.com/cs/14592-lean-mapper-tenke-orm-nad-dibi?p=9#p108540)

* Odstraněna přežitá metoda `LeanMapper\Repository::getEntityClass`, je žádoucí použít `LeanMapper\IMapper::getEntityClass` (BC break)

	[Informace na fóru](https://forum.dibiphp.com/cs/14592-lean-mapper-tenke-orm-nad-dibi?p=9#p108540)

* Přidána protected metoda `LeanMapper\Entity::initDefaults`

	[Informace na fóru](https://forum.dibiphp.com/cs/14592-lean-mapper-tenke-orm-nad-dibi?p=4#p105770)

* Přidána podpora pro výchozí hodnoty uvedené v anotacích

	[Informace na fóru](https://forum.dibiphp.com/cs/14592-lean-mapper-tenke-orm-nad-dibi?p=9#p108616)

* Přidána podpora pro správu jednoduchých M:N vazeb

	[Informace na fóru](https://forum.dibiphp.com/cs/14592-lean-mapper-tenke-orm-nad-dibi?p=4#p105942)

* Přidána podpora pro single table inehritance

	[Informace na fóru](https://forum.dibiphp.com/cs/14592-lean-mapper-tenke-orm-nad-dibi?p=5#p106593)

* Vylepšena podpora výčtového typu (přidána metoda `LeanMapper\Reflection\Property::getEnumValues`)

	[Informace na fóru](https://forum.dibiphp.com/cs/14592-lean-mapper-tenke-orm-nad-dibi?p=6#p107052)

* Zlepšen parser anotací, přidány nové příznaky a odstraněn příznak `m:extra` (BC break)

	[Informace na fóru](https://forum.dibiphp.com/cs/14592-lean-mapper-tenke-orm-nad-dibi?p=8#p107950)

* Přidán whitelist do metody `LeanMapper\Entity::getData`

	[Informace na fóru](https://forum.dibiphp.com/cs/14592-lean-mapper-tenke-orm-nad-dibi?p=8#p107950)

* Přepracován systém filtrů – nové třídy `LeanMapper\Connection` a `LeanMapper\Fluent` (BC break)

	[Informace na fóru](https://forum.dibiphp.com/cs/14592-lean-mapper-tenke-orm-nad-dibi?p=9#p108425) \\
	[Informace na fóru](https://forum.dibiphp.com/cs/14592-lean-mapper-tenke-orm-nad-dibi?p=9#p108493)

* Přidán systém událostí

	[Informace na fóru](https://forum.dibiphp.com/cs/14592-lean-mapper-tenke-orm-nad-dibi?p=10#p108822) \\
	[Propojení s Kdyby\Events](https://forum.nette.org/cs/15165-observer-v-nette-mam-spravny-navrh#p108884)

* Provedena dekompozice `LeanMapper\Repository` (vyčleněny protected metody `Repository::insertIntoDatabase`, `Repository::updateInDatabase`, `Repository::deleteFromDatabase`)

	[Informace na fóru](https://forum.dibiphp.com/cs/14592-lean-mapper-tenke-orm-nad-dibi?p=11#p109028)

* Přejmenovány metody `markAsCreated` na `markAsAttached` (BC break)

	[Informace na fóru](https://forum.dibiphp.com/cs/14592-lean-mapper-tenke-orm-nad-dibi?p=11#p109108)

* Přidána metoda `LeanMapper\Entity::__isset`

	[Informace na fóru](https://forum.dibiphp.com/cs/14592-lean-mapper-tenke-orm-nad-dibi?p=11#p109211)

* Entity není možné vytvářet z detached `LeanMapper\Row` (BC break)

* Změněna viditelnost několika metod (všechny na méně omezující variantu)

* Zlepšen výkon jádra a in-memory cache

* Vylepšeny chybové hlášky

* Při přístupu k položce mají metody vždy přednost před anotacemi

	[Informace na fóru](https://forum.dibiphp.com/cs/14592-lean-mapper-tenke-orm-nad-dibi?p=8#p107950)


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

* Upraven `LeanMapper\Result` tak, že při získávání souvisejících dat může volitelně využívat „IN“ nebo „UNION“ strategii (viz jak to řeší NotORM a také viz [tento článek](https://www.xaprb.com/blog/2006/12/07/how-to-select-the-firstleastmax-row-per-group-in-sql/)) – je to důležité pro správné limitování a řazení

* Přidán příznak `m:enum` (podpora pro výčtový typ, například `m:enum(self::STATUS_*))` – thx [@JanTvrdik](https://forum.dibiphp.com/cs/14592-lean-mapper-tenke-orm-nad-dibi#p105080)



## [1.3.1](https://github.com/Tharos/LeanMapper/tree/v1.3.1) (10. 6. 2013)

* Přidána anotace `@property-read` pro definici *read only* položek entity

* V anotacích `@property` a `@property-read` v entitě je nově možné upřesnit, na jaký sloupec v `LeanMapper\Row` se položka mapuje (např. `@property string $bookName (book_name)`)

* Opraveny známé chyby
