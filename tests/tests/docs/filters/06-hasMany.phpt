<?php

use Tester\Assert;

$container = require __DIR__ . '/../../../bootstrap.php';
$connection = $container->getConnection();

//// example

class CommonFilter
{
	public static function limit(LeanMapper\Fluent $fluent, $limit)
	{
		$fluent->limit($limit);
	}


	public static function orderBy(LeanMapper\Fluent $fluent, $column)
	{
		$fluent->orderBy('%n', $column);
	}
}

$connection->registerFilter('limit', ['CommonFilter', 'limit']);
$connection->registerFilter('orderBy', ['CommonFilter', 'orderBy']);


/**
 * @property int $id
 * @property string $name
 */
class Tag extends LeanMapper\Entity
{
}

/**
 * @property int $id
 * @property string $name
 * @property Tag[] $tags m:hasMany m:filter(limit#10|orderBy#name)
 */
class Book extends LeanMapper\Entity
{
}


//// test

class BookRepository extends TestRepository
{
}

$bookRepository = $container->createRepository('BookRepository');
$books = $bookRepository->findAll();
$book = reset($books);

$book->tags;

Assert::same(array(
	'SELECT * FROM [book]',
	'SELECT [book_tag].* FROM [book_tag] WHERE [book_tag].[book_id] IN (1, 2, 3, 4, 5) LIMIT 10',
	'SELECT [tag].* FROM [tag] WHERE [tag].[id] IN (1, 2) ORDER BY [name]',
), $container->getQueries()->getAll());
