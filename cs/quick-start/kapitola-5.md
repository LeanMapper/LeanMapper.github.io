---
title: Kapitola V – Ukázky použití (a položených SQL dotazů)
redirect_from: "/quick-start/kapitola-5"
---

* [Úvod](#page-title)
* [Přehled knih a výpůjček](#toc-prehled-knih-a-vypujcek)
* [Autorství a recenzenství](#toc-autorstvi-a-recenzenstvi)
* [Tagy, které souvisí s autory](#toc-tagy-ktere-souvisi-s-autory)


Ukážeme si nyní pár ukázek, jak se s tím, co jsme v předchozích kapitolách vytvořili, dá pracovat.

Abyste při vlastních experimentech obdrželi stejný výstup jako je zde v quick startu, použijte připravenou [SQLite databázi včetně dat](/cs/quick-start/kapitola-2/) a na začátek každého skriptu vložte následujících pár řádků kódu:

``` php
<?php

function write($value, $indent = 0) {
	echo str_repeat(' ', $indent), $value, "\n";
}

function separate() {
	echo "\n-----\n\n";
}

$connection = new \LeanMapper\Connection(array(
	'driver' => 'sqlite3',
	'database' => __DIR__ . '/path-to-database/quickstart.sq3',
));
$mapper = new \LeanMapper\DefaultMapper;
$entityFactory = new \LeanMapper\DefaultEntityFactory;

header('Content-type: text/plain;charset=utf8');
```

Jedná se jen o dvě „helper“ funkce pro vypisování hodnot a o vytvoření připojení k databázi.


## Přehled knih a výpůjček {#toc-prehled-knih-a-vypujcek}

### Zadání

Vypište všechny knihy a u každé uveďte jejího autora a seznam výpůjček. U každé výpůjčky uveďte datum vypůjčení.


### Řešení

``` php
<?php

$bookRepository = new BookRepository($connection, $mapper, $entityFactory);

foreach ($bookRepository->findAll() as $book) {
	write($book->name);
	write('Autor: ' . $book->author->name);
	write('Výpůjčky:');
	foreach ($book->borrowings as $borrowing) {
		write($borrowing->borrower->name . '(' . $borrowing->date . ')', 3);
	}
	separate();
}
```


### Výstup

```
The Pragmatic Programmer
Autor: Andrew Hunt
Výpůjčky:
   Vojtech Kohout(2012-04-01)
   Jane Roe(2012-05-06)

-----

The Art of Computer Programming
Autor: Donald Knuth
Výpůjčky:

-----

Refactoring: Improving the Design of Existing Code
Autor: Martin Fowler
Výpůjčky:
   Vojtech Kohout(2013-01-02)

-----

Introduction to Algorithms
Autor: Thomas H. Cormen
Výpůjčky:
   Jane Roe(2012-03-06)
   Jane Roe(2012-05-06)

-----

UML Distilled
Autor: Martin Fowler
Výpůjčky:

-----
```


### Položené SQL dotazy

``` sql
SELECT * FROM [book]
SELECT * FROM [author] WHERE [author].[id] IN (1, 2, 3, 5)
SELECT * FROM [borrowing] WHERE [borrowing].[book_id] IN (1, 2, 3, 4, 5)
SELECT * FROM [borrower] WHERE [borrower].[id] IN (1, 3)
```


## Autorství a recenzenství {#toc-autorstvi-a-recenzenstvi}

### Zadání

Vypište všechny známé autory (respektive recenzenty) a u každého uveďte, kterých knih je autorem a které knihy recenzoval.


### Řešení

``` php
<?php

$authorRepository = new AuthorRepository($connection, $mapper, $entityFactory);

foreach ($authorRepository->findAll() as $author) {
	write($author->name);
	write('Je autorem:');
	foreach ($author->books as $book) {
		write($book->name, 3);
	}
	write('Recenzoval:');
	foreach ($author->reviewedBooks as $book) {
		write($book->name, 3);
	}
	separate();
}
```


### Výstup

```
Andrew Hunt
Je autorem:
   The Pragmatic Programmer
Recenzoval:
   The Art of Computer Programming

-----

Donald Knuth
Je autorem:
   The Art of Computer Programming
Recenzoval:

-----

Martin Fowler
Je autorem:
   Refactoring: Improving the Design of Existing Code
   UML Distilled
Recenzoval:

-----

Kent Beck
Je autorem:
Recenzoval:
   Refactoring: Improving the Design of Existing Code

-----

Thomas H. Cormen
Je autorem:
   Introduction to Algorithms
Recenzoval:

-----
```


### Položené SQL dotazy

``` sql
SELECT * FROM [author]
SELECT * FROM [book] WHERE [book].[author_id] IN (1, 2, 3, 4, 5)
SELECT * FROM [book] WHERE [book].[reviewer_id] IN (1, 2, 3, 4, 5)
```


## Tagy, které souvisí s autory {#toc-tagy-ktere-souvisi-s-autory}

### Zadání

Vypište všechny známé autory (respektive recenzenty) a ke každému z nich vypište tagy, které s ním souvisí. To, že s autorem tag souvisí, znamená, že daný tag je přiřazen ke knize, kterou napsal nebo recenzoval.


### Řešení

``` php
<?php

$authorRepository = new AuthorRepository($connection, $mapper, $entityFactory);

foreach ($authorRepository->findAll() as $author) {
	write($author->name);
	foreach ($author->referencingTags as $tag) {
		write($tag->name, 3);
	}
	separate();
}
```


### Výstup

```
Andrew Hunt
   popular
   ebook

-----

Donald Knuth

-----

Martin Fowler
   ebook

-----

Kent Beck
   ebook

-----

Thomas H. Cormen
   popular

-----
```


### Položené SQL dotazy

``` sql
SELECT * FROM [author]
SELECT * FROM [book_tag] WHERE [book_tag].[book_id] IN (1, 2, 3, 4, 5)
SELECT * FROM [tag] WHERE [tag].[id] IN (1, 2)
SELECT * FROM [book_tag] WHERE [book_tag].[book_id] IN (2, 3)
SELECT * FROM [tag] WHERE [tag].[id] IN (2)
```


[« Definice repositářů](/cs/quick-start/kapitola-4/) |
