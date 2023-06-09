---
title: Rozšíření, addony
rank: 40
---

_Některá rozšíření naleznete také na fóru v sekci [Rozšíření](https://leanmapper-forum.intm.org/t/rozsireni)._

<div class="cards">
	{% assign addons = site.addons | where:'active', true %}
	{% for addon in addons %}
		<div class="card">
			<h2 class="card__title"><a href="{% if addon.repository %}{{ addon.repository }}{% else %}{{ addon.link }}{% endif %}">{{ addon.name }}</a></h2>

			<div class="card__content">
				<p>{{ addon.description }}</p>
				{{ addon.content }}
			</div>

			{% if addon.composerName %}
			<div class="card__footer">
				{% if addon.repository %}<a href="{{ addon.repository }}" rel="noopener" target="_blank"><img src="https://poser.pugx.org/{{ addon.composerName }}/v/stable" alt="Latest Stable Version"></a>{% endif %}
				<a href="https://packagist.org/packages/{{ addon.composerName }}" rel="noopener" target="_blank"><img src="https://img.shields.io/packagist/dm/{{ addon.composerName }}.svg" alt="Downloads this Month"></a>
			</div>
			{% endif %}
		</div>
	{% endfor %}
</div>

<hr>

<h2>Neudržovaná rozšíření</h2>

<div class="cards">
	{% assign addons = site.addons | where:'active', false %}
	{% for addon in addons %}
		<div class="card card--inactive">
			<h2 class="card__title"><a href="{% if addon.repository %}{{ addon.repository }}{% else %}{{ addon.link }}{% endif %}">{{ addon.name }}</a></h2>

			<div class="card__content">
				<p>{{ addon.description }}</p>
				{{ addon.content }}
			</div>
		</div>
	{% endfor %}
</div>
