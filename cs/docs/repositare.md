---
title: Repositáře
redirect_from: "/dokumentace/repositare"
---

* [Úvod](#page-title)
* [Vytváříme repositáře](#toc-vytvarime-repositare)
* [Metoda getTable()](#toc-metody)


Repositáře typicky (nikoliv ale nezbytně) reprezentují potomci abstraktní třídy [`LeanMapper\Repository`](https://codedoc.pub/tharos/leanmapper/v3.1.1/class-LeanMapper.Repository.html). Ta obsahuje pouze výchozí implementaci metod pro persistenci a odstranění entity a několik pomocných protected metod usnadňujících definici konkrétních repositářů.

Význam repositářů je následující: v souladu s návrhovým vzorem Data mapper, kterým je Lean Mapper silně inspirován, se entity bez cizí pomoci neumějí načítat ani persistovat (tj. ukládat se do databáze nebo se z ní mazat). K tomu jsou zapotřebí repositáře. Repositáře mají za úkol načítat entity z databáze a pokud se nějaká entita v průběhu svého života změní, mají za úkol umožnit uložení těchto změn do databáze. Zodpovědností repositářů je také entity trvale odstraňovat a vytvářet zbrusu nové.

Z výše uvedeného pravidla existuje **jedna jediná výjimka** – entita si dokáže sama načíst entity, ke kterým má nadefinovanou vazbu.


## Vytváříme repositáře {#toc-vytvarime-repositare}

Jelikož persistenci za nás běžně kompletně řeší podědění třídy `LeanMapper\Repository`, zbývá typicky už jen nadefinovat konkrétní metody pro načítání entit. Běžně se každá taková metoda sestává ze dvou pomyslných částí:

1. Načtení požadované relace z databáze (pomocí `LeanMapper\Connection`, které je v repositáři dědícím od `LeanMapper\Repository` dostupné)
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


## Metoda getTable() {#toc-metody}

Pokud dodržujete [konvence](/cs/docs/konvence/), již názvem repositáře říkáte, o jaké entity se repositář stará a v jaké databázové tabulce jsou pro tyto entity uložená data. Abyste v každé druhé metodě repositáře nemuseli název tabulky pořád dokola opakovat, `LeanMapper\Repository` obsahuje protected metodu `getTable()`, která získání názvu tabulky usnadňuje.

Tuto metodu lze velmi dobře využít i tehdy, pokud konvence nejsou dodrženy. Název tabulky lze upřesnit v [mapperu](/cs/docs/mapper/) - slouží k tomu metoda `getTableByRepositoryClass`. Ta dostane ve svém parametru název repositáře (např. `MyApplication\Model\Repository\BookRepository`) a vrátí název databázové tabulky.

``` php?start_inline=1
class MyMapper extends \LeanMapper\DefaultMapper
{
	public function getTableByRepositoryClass($repositoryClass)
	{
		if ($repositoryClass === 'MyApplication\Model\Repository\BookRepository') {
			return 'mybook';
		}
		return parent::getTableByRepositoryClass($repositoryClass);
	}
}
```

**Poznámka:** Název tabulky vracený metodou `getTable()` lze upřesnit také pomocí anotace `@table` nad třídou repositáře, tuto variantu však obecně nedoporučujeme.

``` php?start_inline=1
/**
 * @table mybook
 */
class BookRepository extends \LeanMapper\Repository
{
}
```


[« Entity](/cs/docs/entity/) | [Konvence »](/cs/docs/konvence/)
