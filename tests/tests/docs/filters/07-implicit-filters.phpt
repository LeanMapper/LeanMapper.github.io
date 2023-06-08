<?php

use Tester\Assert;

$container = require __DIR__ . '/../../../bootstrap.php';
$connection = $container->getConnection();

//// example

/**
 * @property int $id
 * @property string $name
 * @property bool $available
 */
class Book extends LeanMapper\Entity
{
}

/**
 * @property int $id
 * @property string $name
 * @property Book[] $books m:belongsToMany
 */
class Author extends LeanMapper\Entity
{
}

class CommonFilter
{
	public static function restrictAvailables(LeanMapper\Fluent $fluent, $table)
	{
		$fluent->where('%n.[available] = 1', $table);
	}
}

$connection->registerFilter('restrictAvailables', ['CommonFilter', 'restrictAvailables']);

class Mapper extends LeanMapper\DefaultMapper
{
	protected $defaultEntityNamespace = null; // test


	public function getImplicitFilters($entityClass, LeanMapper\Caller $caller = null)
	{
		if ($entityClass === 'Book') {
			return new LeanMapper\ImplicitFilters(
				['restrictAvailables'],
				[
					'restrictAvailables' => [
						$this->getTable($entityClass),
					],
				]
			);
		}
		return parent::getImplicitFilters();
	}
}

$mapper = new Mapper;


//// test

class AuthorRepository extends TestRepository
{
}

$authorRepository = new AuthorRepository($container->getConnection(), $mapper, $container->getEntityFactory());
$authors = $authorRepository->findAll();
$author = reset($authors);

//// example
$books = $author->books;
//// \example

Assert::same([
	'SELECT * FROM [author]',
	'SELECT [book].* FROM [book] WHERE [book].[author_id] IN (1, 2, 3, 4, 5) AND [book].[available] = 1',
], $container->getQueries()->getAll());
