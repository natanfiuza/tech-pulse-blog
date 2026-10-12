# TechPulse Newsletter — Edition #02

## 👋 Personal opening

Hey, dev!

In the last edition the newsletter was an experiment. Now every subscriber receives the body in their own language — and that forced me to solve a small but real architecture problem, which I tell below.

On the blog, the week brought `git bisect`, debugging APIs from the terminal, the JavaScript runtimes and the EU AI Act. This edition's tip is the evolution of #01's.

Let's get straight to the point!

---

## 💡 Tip of the week

**Three Eloquent guards in a single line**

In edition #01 I showed how to block lazy loading. One call turns that behaviour on plus two more:

```php
public function boot(): void
{
    Model::shouldBeStrict(! app()->isProduction());
}
```

It enables three guards: `preventLazyLoading`, `preventSilentlyDiscardingAttributes` — which throws when you save an attribute that does not exist in the table, instead of silently discarding it — and `preventAccessingMissingAttributes`, which warns when you read something the query did not load.

The second one catches the most: how many times has a `fill()` swallowed a misspelled field, and you only saw it in production?

---

## 📝 From the TechPulse blog

What came out on the blog:

- **git bisect: Encontre o Commit Exato Que Quebrou a Aplicação em Segundos** — After fifty merged commits, hunting by hand costs over an hour. `git bisect` isolates the culprit in six steps. [Read the full article](https://tech-pulse.natanfiuza.dev.br/post/show/git-bisect-encontre-commit-exato-que-quebrou-aplicacao-segundos)
- **Do DevTools ao Terminal: Como Dissecar Gargalos de APIs com o cURL Nativo** — Copying headers by hand is a nightmare. See how to export the request from DevTools and diagnose latency from the terminal. [Read the full article](https://tech-pulse.natanfiuza.dev.br/post/show/devtools-ao-terminal-como-dissecar-gargalos-apis-curl-nativo)
- **A WinterCG e o Tratado de Paz dos Runtimes JavaScript** — Node, Deno and Bun were heading for a war of proprietary APIs. WinterCG turned the game into a convergence around native Web APIs. [Read the full article](https://tech-pulse.natanfiuza.dev.br/post/show/wintercg-e-tratado-paz-dos-runtimes-javascript)
- **O Manual Técnico do EU AI Act: Proteção Real ou Reserva de Mercado?** — Does European regulation protect society, or does it work as a barrier that suffocates startups and consolidates Big Tech? [Read the full article](https://tech-pulse.natanfiuza.dev.br/post/show/manual-tecnico-eu-ai-act-protecao-real-ou-reserva-mercado)

> Not following the blog yet? Keep up with new posts at [tech-pulse.natanfiuza.dev.br](https://tech-pulse.natanfiuza.dev.br).

---

## 🔍 Weekly curation

- **git-bisect — official documentation** — After the article, take a look at `git bisect run` with an automated test script. [Read the docs](https://git-scm.com/docs/git-bisect)
- **WinterTC** — The group born as WinterCG that now defines interoperability between JavaScript runtimes. [Visit the site](https://wintertc.org/)
- **EU AI Act — Regulatory Framework** — The European Commission's official page, with the phased application timeline. [Read the document](https://digital-strategy.ec.europa.eu/en/policies/regulatory-framework-ai)

---

## 🛠️ Behind the code

**Why Portuguese has no file of its own in the newsletter**

This week the newsletter started going out in each subscriber's language. It lives in the `lang` column, and the send command looks for the body in `newsletter_body.{lang}.md`.

The interesting decision was where to put Portuguese. The obvious path would be to create `newsletter_body.pt_br.md`, mirroring the other languages. That is not what I did.

The resolver looks in this order: the language file, then `newsletter_body.md`, then `.pt_br.md` and `.en.md`. The base file is the **second** in line — it is the universal fallback.

If Portuguese lived in `pt_br.md`, a subscriber with an unsupported language — `de`, `it` — would fall through to `.en.md`. Keeping pt-BR in the base means they get Portuguese: the author's language is the sensible default for someone with no translation.

The cost is that `body_exists()` has to ignore the fallback to answer "does this edition have a dedicated translation?" — one extra line so it does not lie about what is on disk.

---

## 💬 Your turn

Which tool in your stack have you used for years and still discover something new about?

Reply to this email. The best topics become topics for the next edition!

---

## 🔁 Before you go

Forward this to that fellow dev who spends the day hunting bugs through Git history.

See you next Monday!

— **Nataniel Fiuza**  
*Software Developer and Solutions Architect*  
[tech-pulse.natanfiuza.dev.br](https://tech-pulse.natanfiuza.dev.br)
