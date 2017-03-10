---
title: Kapitola IV – Definice repositářů
redirect_from: "/quick-start/kapitola-4"
---

Jak už jeho název napovídá, Lean Mapper je silně inspirován návrhovým vzorem Data Mapper. Proto se v něm entity **neumějí samy vytvářet, persistovat, mazat a v podstatě ani načítat** (načítání je tak trochu výjimkou, protože entita si dokáže sama načíst entity, ke kterým má nadefinovanou vazbu). To je důvod, proč potřebujeme repositáře.

``` php
<?php

namespace Model\Repository;

abstract class Repository extends \LeanMapper\Repository
{

	public function find($id)
	{
		$row = $this->connection->select('*')
			->from($this->getTable())
			->where('id = %i', $id)
			->fetch();

		if ($row === false) {
			throw new \Exception('Entity was not found.');
		}
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

class AuthorRepository extends Repository
{
}

class BookRepository extends Repository
{
}

class BorrowerRepository extends Repository
{
}

class BorrowingRepository extends Repository
{
}

class TagRepository extends Repository
{
}
```

To je vše. S touto šesticí tříd si v našem quick startu bohatě vystačíme.

Abstraktní třída [`LeanMapper\Repository`](https://codedoc.pub/tharos/leanmapper/v3.1.1/class-LeanMapper.Repository.html) obsahuje řadu užitečných metod, které nám ve většině případů vyřeší vytváření, persistenci a odstraňování entit. Načítání entit ale Lean Mapper v této abstraktní třídě záměrně neřeší, protože požadavky na něj bývají různé. Nicméně obsahuje alespoň podpůrné metody, které lze ve vlastních metodách pro načítání entit výhodně využít.

Určitě nepřehlédněte [část dokumentace věnovanou repositářím](/cs/docs/repositare).


[« Definice entit](/cs/quick-start/kapitola-3/) | [Ukázky použití a položených dotazů »](/cs/quick-start/kapitola-5/)
