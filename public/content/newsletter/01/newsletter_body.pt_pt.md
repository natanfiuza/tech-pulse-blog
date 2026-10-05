# TechPulse Newsletter — Edição #01

## 👋 Abertura pessoal

Olá, dev!

Bem-vindo(a) à primeira edição do **TechPulse**. Depois de quase 30 anos a trabalhar em desenvolvimento de sistemas — de Laravel, Vue e bases de dados relacionais até arquitetura de software e automações com inteligência artificial — decidi criar este espaço para partilhar de forma mais direta o que vou aprendendo no dia a dia.

A ideia aqui não é ser apenas mais uma newsletter genérica com manchetes soltas. É trazer conteúdo técnico aplicável, boas práticas que possa correr no seu terminal ou projeto ainda esta semana, e reflexões sinceras sobre o ecossistema.

Vamos direto ao assunto!

---

## 💡 Dica técnica da semana

**Evite N+1 queries no Eloquent com `preventLazyLoading`**

Em desenvolvimento, é comum deixarmos passar relacionamentos carregados dentro de ciclos (N+1). Para nunca mais deixar isso chegar a produção, ative o bloqueio global no `AppServiceProvider`:

```php
public function boot(): void
{
    Model::preventLazyLoading(! app()->isProduction());
}
```

Com esta única linha, o Laravel lança uma exceção imediata ao tentar fazer lazy loading de qualquer relação em ambiente local ou de testes, garantindo que utilize `with()` ou `loadMissing()`.

---

## 📝 Do blog TechPulse

Veja o que saiu no blog nas últimas semanas:

- **O PostgreSQL 19 Não Aposentou o pgvector** — O PostgreSQL 19 não trouxe tipos vetoriais nem índices HNSW para o core. A manchete real da versão é o REPACK — e ele muda a vida de quem guarda embeddings no Postgres. [Ler o artigo completo](https://tech-pulse.natanfiuza.dev.br/post/show/postgresql-19-nao-aposentou-pgvector)
- **O PHP Tem Duas Datas de Morte, Não Uma** — A 31 de dezembro de 2026, o PHP 8.2 chega ao fim de tudo e o PHP 8.4 sai do suporte ativo. Duas fronteiras na mesma data, e só uma delas é um abismo a sério. [Ler o artigo completo](https://tech-pulse.natanfiuza.dev.br/post/show/php-tem-duas-datas-morte-nao)
- **O Domínio de Exemplo que Ninguém Comprou Ainda** — Quem escreve example.com usa um nome que a RFC 2606 reservou em 1999; quem escreve yoursite.com usa um nome que qualquer pessoa pode comprar hoje. Cerca de 359 mil ficheiros no GitHub escolheram a segunda opção. [Ler o artigo completo](https://tech-pulse.natanfiuza.dev.br/post/show/dominio-exemplo-que-ninguem-comprou-ainda)
- **Autorregulação da IA: Quem É Auditado Escreve a Auditoria** — Três concorrentes vão criar em conjunto o órgão que decide se o produto delas é seguro. O modelo citado é a FINRA — mas as duas coisas que dão dentes à FINRA ficaram de fora do desenho. [Ler o artigo completo](https://tech-pulse.natanfiuza.dev.br/post/show/autorregulacao-ia-quem-e-auditado-escreve-auditoria)

> Ainda não segue o blog? Acompanhe as novas publicações e tutoriais técnicos em [tech-pulse.natanfiuza.dev.br](https://tech-pulse.natanfiuza.dev.br).

---

## 🔍 Curadoria da semana

Ligações e leituras que valeram o meu tempo nos últimos dias:

- **Laravel 11 Reverb & Serverless WebSockets** — Guia definitivo de websockets escaláveis e nativos em PHP. [Aceder à documentação](https://laravel.com/docs/11.x/reverb)
- **Vite 6 & The Future of Frontend Tooling** — O que muda com a nova versão do bundler mais rápido da web. [Ver anúncio](https://vite.dev/blog)
- **RFC 8058: One-Click Unsubscribe** — Como os grandes fornecedores (Gmail/Yahoo) priorizam a entregabilidade de e-mails em conformidade. [Ler RFC](https://datatracker.ietf.org/doc/html/rfc8058)

---

## 🛠️ Por trás do código

**O dia em que um UUID salvou a nossa integridade de dados**

Durante a refatoração do nosso sistema de upload e serving de imagens embutidas no editor Markdown, deparámo-nos com o desafio do slug mutável: quando o autor alterava o título do post, todos os links das imagens antigas quebravam.

A solução foi separar o identificador público da entidade da sua representação visual: o `{slug}` na rota é meramente decorativo para SEO, enquanto o backend resolve e limpa os ficheiros unicamente pelo `{uuid}` de 36 caracteres. Simplicidade arquitetural que previne bugs silenciosos.

---

## 💬 Sua vez

Usa IA no seu dia a dia de desenvolvimento — e em que momento desconfia dela?

Responda diretamente a este e-mail. Leio todas as respostas pessoalmente e os melhores temas viram artigos no blog e pauta para a próxima edição!

---

## 🔁 Antes de fechar

Se este conteúdo lhe foi útil, partilhe com um colega dev. Isso ajuda imenso a comunidade TechPulse a crescer.

Vemo-nos na próxima segunda-feira!

— **Nataniel Fiuza**  
*Programador de Software e Arquiteto de Soluções*  
[tech-pulse.natanfiuza.dev.br](https://tech-pulse.natanfiuza.dev.br)
