<?php

use Tester\Assert;

$container = require __DIR__ . '/../../../bootstrap.php';
$connection = $container->getConnection();

//// example

/**
 * @property int $id
 * @property string $name
 * @property Author $author m:hasOne
 */
class Book extends LeanMapper\Entity
{
}

/**
 * @property int $id
 * @property string $name
 * @property Book[] $books m:belongsToMany m:filter(bookOrderByName, bookOnlyAvailable)
 */
class Author extends LeanMapper\Entity
{
}


$connection->registerFilter('bookOrderByName', ['BookFilter', 'orderByName']);


class BookFilter
{
	public static function orderByName(LeanMapper\Fluent $fluent)
	{
		$fluent->orderBy('[name]');
	}
}


//// test
$connection->registerFilter('bookOnlyAvailable', function ($fluent) {
	$fluent->where('[available] = 1');
});

class AuthorRepository extends TestRepository
{
}

$authorRepository = $container->createRepository('AuthorRepository');
$authors = $authorRepository->findAll();
$author = reset($authors);

$author->books;

Assert::same([
	'SELECT * FROM [author]',
	'SELECT [book].* FROM [book] WHERE [book].[author_id] IN (1, 2, 3, 4, 5) AND [available] = 1 ORDER BY [name]',
], $container->getQueries()->getAll());
