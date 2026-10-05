# TechPulse Newsletter — Édition #01

## 👋 Ouverture personnelle

Salut, dev !

Bienvenue dans la première édition de **TechPulse**. Après près de 30 ans à travailler dans le développement de systèmes — de Laravel, Vue et bases de données relationnelles à l'architecture logicielle et aux automatisations avec l'intelligence artificielle — j'ai décidé de créer cet espace pour partager plus directement ce que j'apprends au quotidien.

L'idée n'est pas d'être une énième newsletter générique. C'est d'apporter du contenu technique applicable, des bonnes pratiques que vous pouvez exécuter dans votre terminal ou votre projet dès cette semaine, et des réflexions sincères sur l'écosystème.

Allons droit au but !

---

## 💡 Astuce technique de la semaine

**Évitez les requêtes N+1 dans Eloquent avec `preventLazyLoading`**

En développement, il est courant de laisser passer des relations chargées dans des boucles (N+1). Pour que cela n'atteigne jamais la production, activez le blocage global dans `AppServiceProvider` :

```php
public function boot(): void
{
    Model::preventLazyLoading(! app()->isProduction());
}
```

Avec cette seule ligne, Laravel lève une exception immédiate dès qu'une relation est chargée en lazy loading dans un environnement local ou de test, garantissant l'usage de `with()` ou `loadMissing()`.

---

## 📝 Du blog TechPulse

Voici ce qui est sorti sur le blog ces dernières semaines :

- **O PostgreSQL 19 Não Aposentou o pgvector** — PostgreSQL 19 n'a apporté ni types vectoriels ni index HNSW au core. Le vrai titre de cette version, c'est REPACK — et il change la vie de ceux qui stockent des embeddings dans Postgres. [Lire l'article complet](https://tech-pulse.natanfiuza.dev.br/post/show/postgresql-19-nao-aposentou-pgvector)
- **O PHP Tem Duas Datas de Morte, Não Uma** — Le 31 décembre 2026, PHP 8.2 arrive en fin de vie et PHP 8.4 quitte le support actif. Deux frontières à la même date, et une seule est un vrai précipice. [Lire l'article complet](https://tech-pulse.natanfiuza.dev.br/post/show/php-tem-duas-datas-morte-nao)
- **O Domínio de Exemplo que Ninguém Comprou Ainda** — Écrire example.com, c'est utiliser un nom que la RFC 2606 a réservé en 1999 ; écrire yoursite.com, c'est utiliser un nom que n'importe qui peut acheter aujourd'hui. Environ 359 mille fichiers sur GitHub ont choisi la seconde option. [Lire l'article complet](https://tech-pulse.natanfiuza.dev.br/post/show/dominio-exemplo-que-ninguem-comprou-ainda)
- **Autorregulação da IA: Quem É Auditado Escreve a Auditoria** — Trois concurrents vont créer ensemble l'organisme qui décide si leur produit est sûr. Le modèle cité est la FINRA — mais les deux choses qui donnent des dents à la FINRA sont restées hors du schéma. [Lire l'article complet](https://tech-pulse.natanfiuza.dev.br/post/show/autorregulacao-ia-quem-e-auditado-escreve-auditoria)

> Vous ne suivez pas encore le blog ? Retrouvez les nouvelles publications et les tutoriels techniques sur [tech-pulse.natanfiuza.dev.br](https://tech-pulse.natanfiuza.dev.br).

---

## 🔍 Sélection de la semaine

Liens et lectures qui ont valu mon temps ces derniers jours :

- **Laravel 11 Reverb & Serverless WebSockets** — Le guide définitif des WebSockets scalables et natifs en PHP. [Voir la documentation](https://laravel.com/docs/11.x/reverb)
- **Vite 6 & The Future of Frontend Tooling** — Ce qui change avec la nouvelle version du bundler le plus rapide du web. [Voir l'annonce](https://vite.dev/blog)
- **RFC 8058: One-Click Unsubscribe** — Comment les grands fournisseurs (Gmail/Yahoo) priorisent la délivrabilité des e-mails conformes. [Lire la RFC](https://datatracker.ietf.org/doc/html/rfc8058)

---

## 🛠️ Derrière le code

**Le jour où un UUID a sauvé notre intégrité de données**

Pendant la refonte de notre système d'images intégrées à l'éditeur Markdown, nous avons rencontré le problème du slug mutable : quand l'auteur changeait le titre d'un article, tous les liens des anciennes images cassaient.

La solution a été de séparer l'identifiant public de l'entité de sa représentation visuelle : le `{slug}` dans la route est purement décoratif pour le SEO, tandis que le backend résout et nettoie les fichiers uniquement par l'`{uuid}` de 36 caractères. Une simplicité architecturale qui évite des bugs silencieux.

---

## 💬 À vous

Utilisez-vous l'IA au quotidien dans votre développement — et à quel moment vous en méfiez-vous ?

Répondez directement à cet e-mail. Je lis toutes les réponses personnellement, et les meilleurs sujets deviennent des articles sur le blog et le thème de la prochaine édition !

---

## 🔁 Avant de fermer

Si ce contenu vous a été utile, partagez-le avec un collègue dev. Cela aide beaucoup la communauté TechPulse à grandir.

On se retrouve lundi prochain !

— **Nataniel Fiuza**  
*Développeur de logiciels et architecte de solutions*  
[tech-pulse.natanfiuza.dev.br](https://tech-pulse.natanfiuza.dev.br)
