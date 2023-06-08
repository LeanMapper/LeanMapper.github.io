<?php

use Tester\Assert;

$container = require __DIR__ . '/../../../bootstrap.php';
$connection = $container->getConnection();

//// example

/**
 * @property int $id
 * @property string $name
 * @property Book[] $books m:belongsToMany(#union)
 */
class Author extends LeanMapper\Entity
{
	public function getNewestBook()
	{
		$books = $this->getValueByPropertyWithRelationship('books', new LeanMapper\Filtering(function (LeanMapper\Fluent $fluent) {
			$fluent->orderBy('pubdate')->desc()
				->limit(1);
		}));
		return empty($books) ? null : reset($books);
	}
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

$author->newestBook;

Assert::same([
	'SELECT * FROM [author]',
	'SELECT * FROM (' . implode(') UNION SELECT * FROM (', [
		'SELECT [book].* FROM [book] WHERE [book].[author_id] = 1 ORDER BY [pubdate] DESC LIMIT 1',
		'SELECT [book].* FROM [book] WHERE [book].[author_id] = 2 ORDER BY [pubdate] DESC LIMIT 1',
		'SELECT [book].* FROM [book] WHERE [book].[author_id] = 3 ORDER BY [pubdate] DESC LIMIT 1',
		'SELECT [book].* FROM [book] WHERE [book].[author_id] = 4 ORDER BY [pubdate] DESC LIMIT 1',
		'SELECT [book].* FROM [book] WHERE [book].[author_id] = 5 ORDER BY [pubdate] DESC LIMIT 1',
	]) . ')',
], $container->getQueries()->getAll());
