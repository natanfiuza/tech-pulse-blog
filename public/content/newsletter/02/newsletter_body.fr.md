# TechPulse Newsletter — Édition #02

## 👋 Ouverture personnelle

Salut, dev !

La newsletter n'était qu'une expérience dans l'édition précédente. Désormais, chaque abonné reçoit le contenu dans sa propre langue — et cela m'a obligé à résoudre un problème d'architecture petit, mais réel, que je raconte plus bas.

Sur le blog, la semaine a apporté `git bisect`, le débogage d'API depuis le terminal, les runtimes JavaScript et l'EU AI Act. L'astuce est l'évolution de celle de la #01.

Allons droit au but !

---

## 💡 Astuce technique de la semaine

**Trois verrous Eloquent en une seule ligne**

Dans l'édition #01, j'ai montré comment bloquer le lazy loading. Un appel active ce comportement et deux autres :

```php
public function boot(): void
{
    Model::shouldBeStrict(! app()->isProduction());
}
```

Cela active trois garde-fous : `preventLazyLoading`, `preventSilentlyDiscardingAttributes` — qui lève une exception lorsqu'on enregistre un attribut inexistant, au lieu de l'écarter en silence — et `preventAccessingMissingAttributes`, qui avertit quand vous lisez ce que la requête n'a pas chargé.

Le deuxième attrape le plus : combien de fois un `fill()` a avalé un champ mal orthographié sans que vous le voyiez qu'en production ?

---

## 📝 Du blog TechPulse

Ce qui est sorti sur le blog :

- **git bisect: Encontre o Commit Exato Que Quebrou a Aplicação em Segundos** — Après cinquante commits fusionnés, la chasse manuelle coûte plus d'une heure. `git bisect` isole le coupable en six étapes. [Lire l'article complet](https://tech-pulse.natanfiuza.dev.br/post/show/git-bisect-encontre-commit-exato-que-quebrou-aplicacao-segundos)
- **Do DevTools ao Terminal: Como Dissecar Gargalos de APIs com o cURL Nativo** — Copier les en-têtes à la main est un cauchemar. Découvrez comment exporter la requête depuis les DevTools et diagnostiquer la latence depuis le terminal. [Lire l'article complet](https://tech-pulse.natanfiuza.dev.br/post/show/devtools-ao-terminal-como-dissecar-gargalos-apis-curl-nativo)
- **A WinterCG e o Tratado de Paz dos Runtimes JavaScript** — Node, Deno et Bun s'engageaient vers une guerre d'API propriétaires. WinterCG a fait basculer le jeu vers une convergence autour des Web APIs natives. [Lire l'article complet](https://tech-pulse.natanfiuza.dev.br/post/show/wintercg-e-tratado-paz-dos-runtimes-javascript)
- **O Manual Técnico do EU AI Act: Proteção Real ou Reserva de Mercado?** — La régulation européenne protège-t-elle la société, ou sert-elle de barrière qui étouffe les startups et consolide les Big Tech ? [Lire l'article complet](https://tech-pulse.natanfiuza.dev.br/post/show/manual-tecnico-eu-ai-act-protecao-real-ou-reserva-mercado)

> Vous ne suivez pas encore le blog ? Retrouvez les nouvelles publications sur [tech-pulse.natanfiuza.dev.br](https://tech-pulse.natanfiuza.dev.br).

---

## 🔍 Sélection de la semaine

- **git-bisect — documentation officielle** — Après l'article, regardez `git bisect run` avec un script de test automatisé. [Lire la documentation](https://git-scm.com/docs/git-bisect)
- **WinterTC** — Le groupe né sous le nom de WinterCG, qui définit aujourd'hui l'interopérabilité entre les runtimes JavaScript. [Visiter le site](https://wintertc.org/)
- **EU AI Act — Regulatory Framework** — La page officielle de la Commission européenne, avec le calendrier d'application par phases. [Lire le document](https://digital-strategy.ec.europa.eu/en/policies/regulatory-framework-ai)

---

## 🛠️ Derrière le code

**Pourquoi le portugais n'a pas de fichier à lui dans la newsletter**

Cette semaine, la newsletter a commencé à partir dans la langue de chaque abonné. Elle vit dans la colonne `lang`, et l'envoi cherche le corps dans `newsletter_body.{lang}.md`.

La décision intéressante était de savoir où placer le portugais. L'évidence serait de créer `newsletter_body.pt_br.md`, en miroir des autres langues. Ce n'est pas ce que j'ai fait.

Le résolveur cherche : le fichier de la langue, puis `newsletter_body.md`, puis `.pt_br.md` et `.en.md`. Le fichier de base est le **deuxième** de la file — c'est le fallback universel.

Si le portugais vivait dans `pt_br.md`, un abonné dont la langue n'est pas prise en charge — `de`, `it` — tomberait sur le `.en.md`. En gardant le pt-BR dans le fichier de base, il reçoit du portugais : la langue de l'auteur est le choix par défaut raisonnable pour qui n'a pas de traduction.

Le coût, c'est que `body_exists()` doit ignorer le fallback pour répondre « cette édition a-t-elle une traduction dédiée ? » — une ligne de code en plus pour ne pas mentir sur ce qui existe sur le disque.

---

## 💬 À vous

Quel outil de votre stack utilisez-vous depuis des années et vous surprend encore ?

Répondez à cet e-mail. Les meilleurs sujets deviennent le thème de la prochaine édition !

---

## 🔁 Avant de fermer

Transmettez-le à ce collègue dev qui passe ses journées à traquer des bugs dans l'historique Git.

On se retrouve lundi prochain !

— **Nataniel Fiuza**  
*Développeur de logiciels et architecte de solutions*  
[tech-pulse.natanfiuza.dev.br](https://tech-pulse.natanfiuza.dev.br)
