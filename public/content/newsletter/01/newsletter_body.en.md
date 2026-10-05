# TechPulse Newsletter — Edition #01

## 👋 Personal opening

Hey, dev!

Welcome to the first edition of **TechPulse**. After nearly 30 years working on software development — from Laravel, Vue and relational databases to software architecture and AI automation — I decided to create this space to share what I learn day to day, in a more direct way.

The idea here is not to be just another generic newsletter with loose headlines. It is to bring applicable technical content, good practices you can run in your terminal or project this very week, and honest reflections on the ecosystem.

Let's get straight to the point!

---

## 💡 Tip of the week

**Avoid N+1 queries in Eloquent with `preventLazyLoading`**

In development, it is common to let relationships loaded inside loops (N+1) slip through. To never let that reach production, enable the global block in `AppServiceProvider`:

```php
public function boot(): void
{
    Model::preventLazyLoading(! app()->isProduction());
}
```

With that single line, Laravel throws an exception immediately when any relationship is lazy loaded in a local or test environment, ensuring you use `with()` or `loadMissing()`.

---

## 📝 From the TechPulse blog

Here is what came out on the blog over the last few weeks:

- **O PostgreSQL 19 Não Aposentou o pgvector** — PostgreSQL 19 brought neither vector types nor HNSW indexes to the core. The real headline of the release is REPACK — and it changes life for anyone storing embeddings in Postgres. [Read the full article](https://tech-pulse.natanfiuza.dev.br/post/show/postgresql-19-nao-aposentou-pgvector)
- **O PHP Tem Duas Datas de Morte, Não Uma** — On December 31, 2026, PHP 8.2 reaches end of life and PHP 8.4 leaves active support. Two boundaries on the same date, and only one of them is a real cliff. [Read the full article](https://tech-pulse.natanfiuza.dev.br/post/show/php-tem-duas-datas-morte-nao)
- **O Domínio de Exemplo que Ninguém Comprou Ainda** — Writing example.com uses a name RFC 2606 reserved back in 1999; writing yoursite.com uses a name anyone can buy today. About 359 thousand files on GitHub chose the second option. [Read the full article](https://tech-pulse.natanfiuza.dev.br/post/show/dominio-exemplo-que-ninguem-comprou-ainda)
- **Autorregulação da IA: Quem É Auditado Escreve a Auditoria** — Three competitors will jointly create the body that decides whether their product is safe. The model cited is FINRA — but the two things that give FINRA teeth were left out of the design. [Read the full article](https://tech-pulse.natanfiuza.dev.br/post/show/autorregulacao-ia-quem-e-auditado-escreve-auditoria)

> Not following the blog yet? Keep up with new posts and technical tutorials at [tech-pulse.natanfiuza.dev.br](https://tech-pulse.natanfiuza.dev.br).

---

## 🔍 Weekly curation

Links and reads that were worth my time lately:

- **Laravel 11 Reverb & Serverless WebSockets** — The definitive guide to scalable, native WebSockets in PHP. [Read the docs](https://laravel.com/docs/11.x/reverb)
- **Vite 6 & The Future of Frontend Tooling** — What changes with the new version of the web's fastest bundler. [See the announcement](https://vite.dev/blog)
- **RFC 8058: One-Click Unsubscribe** — How major providers (Gmail/Yahoo) prioritize deliverability for compliant email. [Read the RFC](https://datatracker.ietf.org/doc/html/rfc8058)

---

## 🛠️ Behind the code

**The day a UUID saved our data integrity**

While refactoring our system for uploading and serving images embedded in the Markdown editor, we ran into the mutable slug problem: when the author changed a post's title, every link to the old images broke.

The solution was to separate the entity's public identifier from its visual representation: the `{slug}` in the route is purely decorative for SEO, while the backend resolves and cleans files solely by the 36-character `{uuid}`. An architectural simplicity that prevents silent bugs.

---

## 💬 Your turn

Do you use AI in your day-to-day development — and at what point do you distrust it?

Reply directly to this email. I read every answer personally, and the best topics become articles on the blog and topics for the next edition!

---

## 🔁 Before you go

If this content was useful to you, share it with a fellow dev. It helps the TechPulse community grow a lot.

See you next Monday!

— **Nataniel Fiuza**  
*Software Developer and Solutions Architect*  
[tech-pulse.natanfiuza.dev.br](https://tech-pulse.natanfiuza.dev.br)
