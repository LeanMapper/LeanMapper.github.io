---
title: Kapitola I – Instalace
---

Doporučený způsob instalace je pomocí [Composeru](http://getcomposer.org/):

```
$ composer require tharos/leanmapper
```

Alternativně si můžete Lean Mapper stáhnout také v sekci [Download](/cs/download/) nebo na [GitHubu](https://github.com/Tharos/LeanMapper).

Třídy Lean Mapperu lze načítat buďto automaticky pomocí PSR-0 (typicky při použití Composeru), anebo je lze načíst všechny najednou pomocí skriptu `loader.php`:

``` php
require_once 'LeanMapper/loader.php';
```

Pokud nepoužíváte Composer, nezapomeňte také zajistit dostupnost dibi.


[« Úvod quick startu](/cs/quick-start/) | [Vytvoření schéma databáze »](/cs/quick-start/kapitola-2/)
