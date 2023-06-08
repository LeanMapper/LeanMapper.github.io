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
}

$connection->registerFilter('limit', ['CommonFilter', 'limit']);


/**
 * @property int $id
 * @property string $name
 * @property Book[] $books m:belongsToMany m:filter(limit)
 */
class Author extends LeanMapper\Entity
{
}


//// test

/**
 * @property int $id
 */
class Book extends LeanMapper\Entity
{
}


class AuthorRepository extends TestRepository
{
}

$authorRepository = $container->createRepository('AuthorRepository');
$authors = $authorRepository->findAll();
$author = reset($authors);

//// example
$books = $author->getBooks(20);
//// \example

Assert::same([
	'SELECT * FROM [author]',
	'SELECT [book].* FROM [book] WHERE [book].[author_id] IN (1, 2, 3, 4, 5) LIMIT 20',
], $container->getQueries()->getAll());
