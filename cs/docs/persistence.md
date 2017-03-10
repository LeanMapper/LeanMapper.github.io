---
title: Persistence
redirect_from: "/dokumentace/persistence"
---

* [Úvod](#page-title)
* [Na co dávat pozor](#toc-na-co-davat-pozor)
* [Persistence pod pokličkou](#toc-persistence-pod-poklickou)
* [Interní záležitosti](#toc-interni-zalezitosti)

Lean Mapper obsahuje vestavěnou podporu pro persistenci hodnot entity, které byly pozměněny, a také pro vytváření nových entit a odstraňování nepotřebných. Máme-li entity a repositáře z [quick startu](/cs/quick-start/), můžeme rovnou přistoupit k ukázkám kódu:

``` php
<?php

use Model\Entity\Author;
use Model\Repository\AuthorRepository;

$connection = new DibiConnection(/*...*/);

$authorRepository = new AuthorRepository($connection);

$author = new Author;
$author->name = 'Robert Martin';
$author->web = 'http://www.objectmentor.com/omTeam/martin_r.html';

// saves new entity into database and returns its unique identifier
$authorId = $authorRepository->persist($author);


$author = $authorRepository->find($authorId);
$author->web = null;

// saves changes into database and returns count of modified rows
$modifiedRowsCount = $authorRepository->persist($author);

// removes author from database
$authorRepository->delete($author);
```

``` php
<?php

use Model\Entity\Book;
use Model\Repository\AuthorRepository;
use Model\Repository\BookRepository;

$connection = new DibiConnection(/*...*/);

$authorRepository = new AuthorRepository($connection);
$bookRepository = new BookRepository($connection);

$author = $authorRepository->find(1);

$book = new Book;

$book->author = $author;
$book->pubdate = '2013-05-10'; // string because of SQLite
$book->name = 'Introduction to Clojure';
$book->available = true;

// saves new book with relationship to author with ID 1
$bookRepository->persist($book);
```

Jak je z ukázek patrné, persistence v Lean Mapperu je naprosto intuitivní.


## Na co dávat pozor {#toc-na-co-davat-pozor}

Aby byla persistence v Lean Mapperu co nejintuitivnější, má určitě specifika.

V Lean Mapperu strikně platí, že entity neumějí samy sebe persistovat – potřebují k tomu repositáře. V následující ukázce se uloží pozměněný název knihy, ale už ne pozměněný název autora:

``` php
<?php

$book = $bookRepository->find(1);

$book->name = 'New book name';

$author = $book->author;

$author->name = 'New author name';

$bookRepository->persist($book);
```

Pokud bychom chtěli uložit i pozměněný název autora, museli bychom někam za jeho přiřazení doplnit ještě volání `$authorRepository->persist($author)`. Důvod je prostý – autor sám sebe persistovat neumí a repositář `BookRepository` umí persistovat pouze knihy.

----------

Dále existuje v Lean Mapperu pravidlo, díky kterému lze v naší ukázce přes výchozí magický `__set` knihy přiřadit pouze takového autora, který už v databázi existuje. Následující kód tedy skončí výjimkou:

``` php
<?php

$book = $bookRepository->find(1);

$author = new Author;
$author->name = 'Dave Kriege';

// following line throws exception
$book->author = $author;
```

Nově vytvořeného autora bychom museli před přiřazením vložit do databáze: `$authorRepository->persist($author)`. Poté by ho už bylo knize možné bez problému předat.

----------

Co postupně vykoná následující kód?

``` php
<?php

$book = $bookRepository->find(1);

$bookRepository->delete($book);

$bookRepository->persist($book);
```

Odstraní z databáze knihu s ID 1 a v zápětí vloží novou knihu se stejnými hodnotami položek, jaké měla odstraněná (pozor – v některých případech ale už ne se všemi vazbami, typicky mohou chybět některé M:N vazby odstraněné kaskádovými cizími klíči v databázi).


## Persistence pod pokličkou {#toc-persistence-pod-poklickou}

Určitě se podívejte na implementaci metody `persist($entity)` v abstraktní třídě `LeanMapper\Repository`. Mechanismus persistence je z ní dobře patrný.

[Entita](https://codedoc.pub/tharos/leanmapper/v3.1.1/class-LeanMapper.Entity.html) vychází repositáři vstříc svými metodami `isModified()`, `isDetached()`, `detach()`, `getModifiedData()` a `markAsCreated($id, $table, $connection)`. Metoda `isModified()` vrací informaci, zda byla data v entitě od okamžiku jejího vytvoření pozměněná, `isDetached()` vrací informaci, zda se jedná o nově vytvořenou entitu nebo v databázi již existující, `detach()` umožňuje prohlásit entitu za nově vytvořenou, `getModifiedData()` vrací pole pozměněných hodnot (ve formátu položka => pozměněná hodnota) a konečně `markAsCreated($id, $table, $connection)` slouží ke změnu stavu entity z nově vytvořené na již uloženou.

Všimněte si parametrů, které přijímá poslední zmíněná metoda. ID záznamu v databázi zná bezprostředně po jeho vytvoření pouze repositář a tímto způsobem ho sděluje entitě. Další dva parametry pak slouží k tomu, aby si entita od té doby mohla sama načítat entity, ke kterým má nadefinovanou vazbu.


## Interní záležitosti {#toc-interni-zalezitosti}

Co vypíše následující kód?

``` php
<?php

$book = $bookRepository->find(1);

$author = $book->author;
$author->name = 'Franta';

$author = $book->author;

echo $author->name;
```

Franta? Nebo snad něco jiného?

Vypíše Franta. Jak byste asi očekávali. ;) Důvodem je to, že ačkoliv první a druhá instance `Model\Entity\Author` skutečně nejsou ekvivalentní, obě operují nad stejnou instancí [`LeanMapper\Result`](https://codedoc.pub/tharos/leanmapper/v3.1.1/class-LeanMapper.Result.html), která zapouzdřuje vlastní data. Chování Lean Mapperu se v podobných situacích snaží být maximálně intuitivní a transparentní.

Dobrá rada na závěr: nikdy neuchovávejte žádné hodnoty entity přímo v entitě, ale vždy je deleguje až do `LeanMapper\Result` (typicky pomocí třídy `LeanMapper\Row`, jejíž instance je přítomná v každé entitě).


[« Konvence](/cs/docs/konvence/) | [Filtry »](/cs/docs/filtry/)
