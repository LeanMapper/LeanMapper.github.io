---
title: Nette Framework
rank: 20
---

* [DI rozšíření](#toc-nette-extension)
* [Ruční definice](#toc-nette-config)
* [Předání repositáře do presenteru](#toc-nette-presenter)


Na této si stránce si ukážeme, jak použít Lean Mapper v rámci aplikace napsané v [Nette](https://nette.org).


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
    db:
        driver: mysqli
        host: localhost
        username: ...
        password: ...
        database: mydatabase

services:
    # registrace Lean Mapperu
    - LeanMapper\Connection(%db%)
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
class BookPresenter extends \Nette\Application\UI\Presenter
{
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
class BookPresenter extends \Nette\Application\UI\Presenter
{
    /** @var \Model\BookRepository @inject */
    public $bookRepository;

    ...
}
```

V rámci presenteru pak máme repositář přístupný přes `$this->bookRepository`.
