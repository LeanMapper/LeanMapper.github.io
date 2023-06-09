---
layout: default
active: home
---

# Novinky na <a href="/blog/">blogu</a>

<div class="posts">
	{% for post in site.posts limit:3 %}
		<div class="post">
			<h2 class="post__title"><a href="{{ post.url }}">{{ post.title }}</a></h2>

			<div class="post__perex">
				<p>{{ post.excerpt | strip_html }}</p>
			</div>

			<div class="post__info">
				<div class="post__author">{{ post.author }}</div>
				<div class="post__date">{{ post.date | date: "%-d. %-m. %Y" }}</div>
			</div>
		</div>
	{% endfor %}
</div>

-----


# Co je Lean Mapper

- **Tenké ORM pro PHP postavené nad knihovnou [Dibi](https://dibiphp.com)**
<br> Prověřená a výkonná knihovna Dibi poskytuje Lean Mapperu *stabilní půdu pod nohama* a umožňuje psát kód pro *širokou řadu databázových systémů*.

- **ORM, které sestavuje elegantní a efektivní SQL dotazy**
<br> Lean Mapper je silně inspirován knihovnou [NotORM](http://www.notorm.com) a obsahuje vlastní minimalistickou implementaci „NotORM principu“ (stahování souvisejících záznamů pro celý výsledek najednou místo jednotlivě).

- **Stabilní konzervativní knihovna**
<br> Stabilita a bezchybná funkčnost má při vývoji Lean Mapperu *nejvyšší prioritu*. Každá vydaná verze je označena kódem ve tvaru X.Y.Z a platí, že *změny v rámci řady Z jsou vždy zpětně kompatibilní*. Opravné balíčky jsou *vždy portovány do všech chybou dotčených řad X a Y*.

- **Knihovna s minimem závislostí**
<br> Jedinou závislostí Lean Mapperu je [Dibi](https://dibiphp.com).


# Co Lean Mapper není

- **Moloch**
<br> Jádro ORM tvoří zhruba deset relativně jednoduchých tříd. Pro mírně pokročilého PHP programátora by měl být vlastní zdrojový kód ORM <em>snadno pochopitelný</em>.

- **Revoluční knihovna**
<br> Lean Mapper není v ničem převratný &ndash; převážně jen integruje osvědčené postupy a návrhové vzory a vyhýbá se těm, které se jinde ukázaly jako problematické.

- **Nezdokumentované cosi**

# Jak začít

1. Přečtete si [quick start](/cs/quick-start/)
2. Podle potřeby nastudujte [podrobnou dokumentaci](/cs/)
