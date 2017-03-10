---
title: Repositáře
redirect_from: "/dokumentace/repositare"
---

* [Úvod](#page-title)
* [Vytváříme repositáře](#toc-vytvarime-repositare)
* [Metody getTable() a getEntityClass()](#toc-metody)


Repositáře typicky (nikoliv ale nezbytně) reprezentují potomci abstraktní třídy [`LeanMapper\Repository`](https://codedoc.pub/tharos/leanmapper/v3.1.1/class-LeanMapper.Repository.html). Ta obsahuje pouze výchozí implementaci metod pro persistenci a odstranění entity a několik pomocných protected metod usnadňujících definici konkrétních repositářů.

Význam repositářů je následující: v souladu s návrhovým vzorem Data mapper, kterým je Lean Mapper silně inspirován, se entity bez cizí pomoci neumějí načítat ani persistovat (tj. ukládat se do databáze nebo se z ní mazat). K tomu jsou zapotřebí repositáře. Repositáře mají za úkol načítat entity z databáze a pokud se nějaká entita v průběhu svého života změní, mají za úkol umožnit uložení těchto změn do databáze. Zodpovědností repositářů je také entity trvale odstraňovat a vytvářet zbrusu nové.

Z výše uvedeného pravidla existuje **jedna jediná výjimka** – entita si dokáže sama načíst entity, ke kterým má nadefinovanou vazbu (a to je také důvod, proč entita hluboko ve svém nitru přece jen udržuje instanci třídy DibiConnection).


## Vytváříme repositáře {#toc-vytvarime-repositare}

Jelikož persistenci za nás běžně kompletně řeší podědění třídy `LeanMapper\Repository`, zbývá typicky už jen nadefinovat konkrétní metody pro načítání entit. Běžně se každá taková metoda sestává ze dvou pomyslných částí:

1. Načtení požadované relace z databáze (pomocí `DibiConnection`, které je v Repositáři dědícím od `LeanMapper\Repository` dostupné)
2. Vytvořit z požadované relace entitu nebo kolekci entit (pole).

V pomyslné druhé části můžeme velmi výhodně využít protected metod `createEntity` a `createEntities`:

``` php
<?php

namespace Model\Repository;

class AuthorRepository extends \LeanMapper\Repository
{

	public function find($id)
	{
		// first part
		$row = $this->connection->select('*')
			->from($this->getTable())
			->where('id = %i', $id)
			->fetch();

		if ($row === false) {
			throw new \Exception('Entity was not found.');
		}
		// second part
		return $this->createEntity($row);
	}

	public function findAll()
	{
		return $this->createEntities(
			$this->connection->select('*')
				->from($this->getTable())
				->fetchAll()
		);
	}

}
```

Snad ani není zapotřebí zmiňovat, že metody pro načítání dat mohou přijímat libovolné parametry, které se mohou zohlednit například při sestavování SQL dotazu – zde se fantazii meze nekladou (může jít o různé filtry, limity, řazení…).


## Metody getTable() a getEntityClass() {#toc-metody}

Pokud dodržujete [konvence](/cs/docs/konvence/), již názvem repositáře říkáte, o jaké entity se repositář stará a v jaké databázové tabulce jsou pro tyto entity uložená data. Abyste v každé druhé metodě repositáře nemuseli tyto názvy pořád dokola opakovat, `LeanMapper\Repository` obsahuje protected metody `getTable()` a `getEntityClass()`, které usnadňují jejich získání.

Tyto metody lze velmi dobře využít i tehdy, pokud konvence nejsou dodrženy. Název tabulky lze upřesnit pomocí anotace `@table` a třídu entity lze upřesnit pomocí anotace `@entity` (obě anotace patří nad třídu repositáře).

``` php
<?php

namespace MyApplication\Model\Repository;

/**
 * @table mybook
 * @entity MyBook
 */
class BookRepository extends \LeanMapper\Repository
{
}
```

Jednu z konvencí lze velmi snadno změnit globálně pro celou aplikaci – název jmenného prostoru, ve kterém sídli entity. Stačí přepsat výchozí hodnotu `Model\Entity` uloženou ve veřejné statické proměnné `$defaultEntityNamespace` třídy `LeanMapper\Repository`:

``` php
<?php

LeanMapper\Repository::$defaultEntityNamespace = 'MyApplication\Model';
```


[« Entity](/cs/docs/entity/) | [Konvence »](/cs/docs/konvence/)
