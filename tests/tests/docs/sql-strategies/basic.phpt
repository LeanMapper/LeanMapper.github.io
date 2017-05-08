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
 * @property Book[] $books m:belongsToMany(#union) m:filter(limit#1)
 */
class Author extends LeanMapper\Entity
{
}


//// test

/**
 * @property int $id
 * @property string $name
 */
class Book extends LeanMapper\Entity
{
}

class AuthorRepository extends TestRepository
{
}

$authorRepository = $container->createRepository('AuthorRepository');
$authors = $authorRepository->findAll();
$books = array();

foreach ($authors as $author) {
	foreach ($author->books as $book) {
		$books[] = array(
			'author' => $author->id,
			'book' => $book->id,
		);
	}
}

Assert::same(array(
	array(
		'author' => 1,
		'book' => 1,
	),

	array(
		'author' => 2,
		'book' => 2,
	),

	array(
		'author' => 3,
		'book' => 3,
	),

	array(
		'author' => 5,
		'book' => 4,
	),
), $books);
