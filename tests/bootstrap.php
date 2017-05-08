<?php

use LeanMapper\Connection;
use LeanMapper\DefaultEntityFactory;
use LeanMapper\DefaultMapper;

require __DIR__ . '/vendor/autoload.php';

Tester\Environment::setup();
date_default_timezone_set('Europe/Prague');


class TestMapper extends DefaultMapper
{
	protected $defaultEntityNamespace = NULL;
}


class Container
{
	private $connection;
	private $entityFactory;
	private $mapper;
	private $queries;


	public function getConnection()
	{
		if (!$this->connection) {
			$this->connection = new Connection(array(
				'driver' => 'sqlite3',
				'database' => __DIR__ . '/db/library.sq3',
			));

			$queries = $this->getQueries();

			$this->connection->onEvent[] = function ($event) use ($queries) {
				$queries->add($event->sql);
			};
		}
		return $this->connection;
	}


	public function getMapper()
	{
		if (!$this->mapper) {
			$this->mapper = new TestMapper;
		}
		return $this->mapper;
	}


	public function getEntityFactory()
	{
		if (!$this->entityFactory) {
			$this->entityFactory = new DefaultEntityFactory;
		}
		return $this->entityFactory;
	}


	public function getQueries()
	{
		if (!$this->queries) {
			$this->queries = new QueriesLog;
		}
		return $this->queries;
	}


	public function createRepository($class)
	{
		return new $class($this->getConnection(), $this->getMapper(), $this->getEntityFactory());
	}
}


class QueriesLog
{
	private $queries = array();


	public function add($query)
	{
		$this->queries[] = $query;
	}


	public function getAll()
	{
		return $this->queries;
	}


	public function reset()
	{
		$this->queries = array();
	}
}


class TestRepository extends LeanMapper\Repository
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


class Helpers
{
	public static function data($entities, array $fields = NULL)
	{
		$result = array();

		if ($fields !== NULL) {
			$fields = array_flip($fields);
		}

		foreach ($entities as $entity) {
			$data = $entity->getData();

			if ($fields !== NULL) {
				$data = array_intersect_key($data, $fields);
			}
		}

		return $result;
	}
}


function test($cb)
{
	$cb();
}

return new Container;
