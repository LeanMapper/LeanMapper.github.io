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

$connection->registerFilter('limit', array('CommonFilter', 'limit'));


/**
 * @property int $id
 * @property string $name
 * @property Book[] $books m:belongsToMany m:filter(limit#10)
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

$author->books;

Assert::same(array(
	'SELECT * FROM [author]',
	'SELECT [book].* FROM [book] WHERE [book].[author_id] IN (1, 2, 3, 4, 5) LIMIT 10',
), $container->getQueries()->getAll());
