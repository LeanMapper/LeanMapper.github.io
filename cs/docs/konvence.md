---
title: Konvence
redirect_from: "/dokumentace/konvence"
---

Lean Mapper se vám bude velmi dobře používat, pokud budete v maximální možné míře dodržovat následující konvence:

* S výjimkou jednoduchých vazebních tabulek se v každé tabulce nachází sloupec `id` nesoucí primární klíč.
* Název „entitní tabulky“ odpovídá názvu entity převedenému na malé znaky (například entita `Book` má data v tabulce `book`, entita `OrderDetail` v tabulce `orderdetail`).
* Sloupec nesoucí cizí klíč ve vazbě N:1 má tvar {cílová tabulka}_id (například `book_id`, `author_id`, `orderdetail_id`).
* Název vazební tabulky má tvar {zdrojová tabulka}_{cílová tabulka}, kde zdrojová tabulka patří entitě obsahující příznak `m:hasMany` (například `book_tag`).
* Název repositáře má tvar {název entity}Repository (například `BookRepository`, `AuthorRepository`, `OrderDetailRepository`).
* Entity sídlí ve jmenném prostoru `Model\Entity`.

Pokud některou z těchto konvencí porušíte, neznamená to, že by se pro vás stal Lean Mapper nepoužitelným. Používané konvence lze ovlivnit pomocí [mapperu](/cs/docs/mapper/), případně u jednotlivých entit pomocí [anotací](/cs/docs/entity/#toc-definice-pomoci-anotaci).


[« Repositáře](/cs/docs/repositare/) | [Persistence »](/cs/docs/persistence/)
