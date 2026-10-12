# TechPulse Newsletter — Edição #02

## 👋 Abertura pessoal

Oi, dev!

Na edição passada a newsletter era um experimento. Agora cada assinante recebe o corpo no próprio idioma — e isso me obrigou a resolver um problema de arquitetura pequeno, mas real, que conto adiante.

No blog, a semana rendeu `git bisect`, depuração de APIs pelo terminal, os runtimes JavaScript e o EU AI Act. A dica é a evolução da #01.

Vamos direto ao ponto!

---

## 💡 Dica técnica da semana

**Três travas do Eloquent em uma única linha**

Na edição #01 mostrei como bloquear lazy loading. Uma chamada liga aquele comportamento e mais dois:

```php
public function boot(): void
{
    Model::shouldBeStrict(! app()->isProduction());
}
```

São três guardas: `preventLazyLoading`, `preventSilentlyDiscardingAttributes` — que lança exceção ao gravar um atributo inexistente, em vez de descartar em silêncio — e `preventAccessingMissingAttributes`, que avisa quando você lê o que a query não trouxe.

A segunda é a que mais pega: quantas vezes um `fill()` engoliu um campo com nome errado e você só viu em produção?

---

## 📝 Do blog TechPulse

O que saiu por lá:

- **git bisect: Encontre o Commit Exato Que Quebrou a Aplicação em Segundos** — Depois de cinquenta commits mesclados, a caça manual custa mais de uma hora. O `git bisect` isola o culpado em seis passos. [Leia o artigo completo](https://tech-pulse.natanfiuza.dev.br/post/show/git-bisect-encontre-commit-exato-que-quebrou-aplicacao-segundos)
- **Do DevTools ao Terminal: Como Dissecar Gargalos de APIs com o cURL Nativo** — Copiar cabeçalhos na mão é um pesadelo. Veja como exportar a requisição do DevTools e diagnosticar latência pelo terminal. [Leia o artigo completo](https://tech-pulse.natanfiuza.dev.br/post/show/devtools-ao-terminal-como-dissecar-gargalos-apis-curl-nativo)
- **A WinterCG e o Tratado de Paz dos Runtimes JavaScript** — Node, Deno e Bun caminhavam para uma guerra de APIs proprietárias. A WinterCG virou o jogo para uma convergência em torno das Web APIs nativas. [Leia o artigo completo](https://tech-pulse.natanfiuza.dev.br/post/show/wintercg-e-tratado-paz-dos-runtimes-javascript)
- **O Manual Técnico do EU AI Act: Proteção Real ou Reserva de Mercado?** — A regulação europeia protege a sociedade ou funciona como barreira que asfixia startups e consolida as Big Techs? [Leia o artigo completo](https://tech-pulse.natanfiuza.dev.br/post/show/manual-tecnico-eu-ai-act-protecao-real-ou-reserva-mercado)

> Ainda não segue o blog? Acompanhe as novas publicações em [tech-pulse.natanfiuza.dev.br](https://tech-pulse.natanfiuza.dev.br).

---

## 🔍 Curadoria da semana

- **git-bisect — documentação oficial** — Depois do artigo, vale ver o `git bisect run` com script de teste automatizado. [Ler a documentação](https://git-scm.com/docs/git-bisect)
- **WinterTC** — O grupo que nasceu como WinterCG e hoje define a interoperabilidade entre runtimes JavaScript. [Visitar o site](https://wintertc.org/)
- **EU AI Act — Regulatory Framework** — A página oficial da Comissão Europeia, com o calendário de aplicação por fase. [Ler o documento](https://digital-strategy.ec.europa.eu/en/policies/regulatory-framework-ai)

---

## 🛠️ Por trás do código

**Por que o português não tem arquivo próprio na newsletter**

Esta semana a newsletter passou a sair no idioma de cada assinante. Ele mora na coluna `lang`, e o envio busca o corpo em `newsletter_body.{lang}.md`.

A decisão interessante foi onde colocar o português. O óbvio seria criar `newsletter_body.pt_br.md`, espelhando os outros idiomas. Não foi o que fiz.

O resolvedor procura: o arquivo do idioma, depois `newsletter_body.md`, depois `.pt_br.md` e `.en.md`. O base é o **segundo** da fila — o fallback universal.

Se o português morasse em `pt_br.md`, um assinante com idioma não suportado — `de`, `it` — cairia no `.en.md`. Mantendo o pt-BR na base, ele recebe português: o idioma do autor é o padrão razoável para quem não tem tradução.

O custo é que `body_exists()` precisa ignorar o fallback para responder "esta edição tem tradução dedicada?" — uma linha a mais para não mentir sobre o que existe em disco.

---

## 💬 Sua vez

Qual ferramenta da sua stack você usa há anos e ainda descobre recurso novo?

Responda a este e-mail. Os melhores temas viram pauta da próxima edição!

---

## 🔁 Antes de fechar

Encaminhe para aquele colega dev que passa o dia caçando bug no histórico do Git.

Nos vemos na próxima segunda-feira!

— **Nataniel Fiuza**  
*Desenvolvedor de Software e Arquiteto de Soluções*  
[tech-pulse.natanfiuza.dev.br](https://tech-pulse.natanfiuza.dev.br)
