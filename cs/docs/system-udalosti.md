---
title: Systém událostí
rank: 100
---

* [Úvod](#page-title)
* [Registrace událostí](#toc-registrace)
	* [Metoda initEvents()](#toc-initEvents)
* [Události](#toc-udalosti)


[Repositáře](/cs/docs/repositare/) v Lean Mapperu nabízejí jednoduchý systém událostí, který umožňuje reagovat na vytvoření entity, její aktualizaci, nebo smazání.


## Registrace událostí {#toc-registrace}

Jako obsluhu události vždy registrujeme nějaký callback - může se jednat o anonymní funkci, metodu objektu apod. Každá zaregistrovaná obslužná funkce dostane jako parametr entitu, se kterou se pracuje. Výjimku tvoří události `onBeforeDelete` a `onAfterDelete`, které kromě entity akceptují i ID databázového záznamu.

``` php?start_inline=1
$authorRepository = new AuthorRepository($connection, $mapper, $entityFactory);

$authorRepository->onBeforePersist[] = function (Author $author) {
	// obsluha udalosti
};
$authorRepository->onAfterCreate[] = [$obj, 'method'];
$authorRepository->onBeforeDelete[] = 'Class::method';
```

Pro každou událost je možné zaregistrovat více obsluh, v takovém případě se volají postupně.


### Metoda initEvents() {#toc-initEvents}

Velmi užitečná je protected metoda `LeanMapper\Repository::initEvents`, která se volá při vytváření instance repositáře a její přetížení umožňuje nadefinovat události pro daný repositář.

``` php?start_inline=1
class AuthorRepository extends LeanMapper\Repository
{
	protected function initEvents()
	{
		$this->onBeforePersist[] = ...;
		$this->onAfterPersist[] = ...;
	}
}
```


## Události {#toc-udalosti}

Pracovat lze s následujícími událostmi:

* onBeforePersist
* onBeforeCreate
* onBeforeUpdate
* onBeforeDelete
* onAfterPersist
* onAfterCreate
* onAfterUpdate
* onAfterDelete

Význam událostí je doufejme dostatečně zřejmý z jejich názvu. Za zmínku stojí, že při persistenci se `beforePersist` a `afterPersist` zavolají vždy, zatímco `beforeCreate`, `afterCreate`, `beforeUpdate` a `afterUpdate` se volají podle situace a podle toho, zda byla nějaká data změněna.

[« SQL strategie](/cs/docs/sql-strategie/) |
