---
title: CamelCase to under_score mapper
---

Existuje mnoho důvodů proč v databázi pojmenovávat *sloupce/tabulky* pouze malými písmeny a jednotlivá slova oddělovat podtržítkem `_` *(tzv. under score konvence)*. Pokud bychom ale na straně PHP chtěli k těmto datům přistupovat pomocí *camel case konvence*, tedy vizuálně oddělovat velkým písmenem na začátku jednotlivých slov, **Lean Mapper** k tomu nabízí všechny potřebné prostředky.

K zprovoznění této funkcionality postačí upravit [**mapper**](/cs/docs/mapper/), tedy třídu, která dědí po `LeanMapper\DefaultMapper` nebo jen implementuje rozhraní `LeanMapper\IMapper`. Nejprve si připravíme funkce, které nám budou převádět `fooBar` na `foo_bar` a obráceně. Můžeme je klidně implementovat jako statické veřejné metody vlastního mapperu.

``` php?start_inline=1
class Mapper extends LeanMapper\DefaultMapper
{
	public static function toUnderScore($str)
	{
		return lcfirst(preg_replace_callback('#(?<=.)([A-Z])#', function ($m) {
			return '_' . strtolower($m[1]);
		}, $str));
	}

	public static function toCamelCase($str)
	{
		return preg_replace_callback('#_(.)#', function ($m) {
			return strtoupper($m[1]);
		}, $str);
	}
```

Nyní je potřeba tyto funkce použít na příslušných místech mapperu.

``` php?start_inline=1
	public function getTable($entityClass)
	{
		return self::toUnderScore($this->trimNamespace($entityClass));
	}

	public function getEntityClass($table, LeanMapper\Row $row = NULL)
	{
		return ($this->defaultEntityNamespace !== NULL ? $this->defaultEntityNamespace . '\\' : '')
			. ucfirst(self::toCamelCase($table)); // Název třídy začíná velkým písmenem
	}

	public function getColumn($entityClass, $field)
	{
		return self::toUnderScore($field);
	}

	public function getEntityField($table, $column)
	{
		return self::toCamelCase($column);
	}

	public function getTableByRepositoryClass($repositoryClass)
	{
		$matches = array();
		if (preg_match('#([a-z0-9]+)repository$#i', $repositoryClass, $matches)) {
			return self::toUnderScore($matches[1]);
		}
		throw new LeanMapper\Exception\InvalidStateException('Cannot determine table name.');
	}
}
```

A to je vše. Nyní se budou položky definované v entitách jako `fooBar` v databázi hledat ve sloupcích `foo_bar`. Obdobně to platí i pro překlad názvu repozitáře na tabulku.
