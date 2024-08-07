---
title: "Lean Mapper 4.1-RC1"
author: janpecha
---

{:.perex}
Vyšla první RC verze nadcházejícího Lean Mapperu 4.1.

[Souhrn novinek na blogu](/blog/2024-08-07-novinky-ve-verzi-4-1/)

## Changelog

* Entity: přidána traita `Initialize` ([#167](https://github.com/Tharos/LeanMapper/pull/167))

* Entity: přidána podpora pro typ `non-empty-string` u položek entity ([#167](https://github.com/Tharos/LeanMapper/pull/167))

* Entity: `settype()` nahrazeno vlastní metodou `Helpers::convertType()` ([#167](https://github.com/Tharos/LeanMapper/pull/167))

* Entity: opraveno pořadí volání kontroly typu a setter pass ([#167](https://github.com/Tharos/LeanMapper/pull/167))

* Entity: přidána podpora pro nullable syntaxi `?Foo` ([#167](https://github.com/Tharos/LeanMapper/pull/167))

* IEntityFactory: vylepšena návratová hodnota u metody `createCollection()` ([#167](https://github.com/Tharos/LeanMapper/pull/167))

---

**Vyzkoušejte prosím RC verzi na svých projektech, pokud se neobjeví žádná komplikace, vyjde cca za týden stabilní verze.**
