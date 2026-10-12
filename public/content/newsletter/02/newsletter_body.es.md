# TechPulse Newsletter — Edición #02

## 👋 Apertura personal

¡Hola, dev!

En la edición anterior el boletín era un experimento. Ahora cada suscriptor recibe el cuerpo en su propio idioma — y eso me obligó a resolver un problema de arquitectura pequeño, pero real, que cuento abajo.

En el blog, la semana dio `git bisect`, depuración de APIs desde el terminal, los runtimes de JavaScript y el EU AI Act. El consejo es la evolución del de la #01.

¡Vamos directo al grano!

---

## 💡 Consejo técnico de la semana

**Tres bloqueos de Eloquent en una sola línea**

En la edición #01 mostré cómo bloquear el lazy loading. Una llamada activa ese comportamiento y dos más:

```php
public function boot(): void
{
    Model::shouldBeStrict(! app()->isProduction());
}
```

Son tres guardas: `preventLazyLoading`, `preventSilentlyDiscardingAttributes` — que lanza una excepción al guardar un atributo inexistente, en vez de descartarlo en silencio — y `preventAccessingMissingAttributes`, que avisa cuando lees lo que la consulta no trajo.

La segunda es la que más pilla: ¿cuántas veces un `fill()` se tragó un campo mal escrito y solo lo viste en producción?

---

## 📝 Del blog TechPulse

Lo que salió en el blog:

- **git bisect: Encontre o Commit Exato Que Quebrou a Aplicação em Segundos** — Después de cincuenta commits fusionados, la caza manual cuesta más de una hora. `git bisect` aísla al culpable en seis pasos. [Leer el artículo completo](https://tech-pulse.natanfiuza.dev.br/post/show/git-bisect-encontre-commit-exato-que-quebrou-aplicacao-segundos)
- **Do DevTools ao Terminal: Como Dissecar Gargalos de APIs com o cURL Nativo** — Copiar cabeceras a mano es una pesadilla. Mira cómo exportar la petición desde DevTools y diagnosticar latencia desde el terminal. [Leer el artículo completo](https://tech-pulse.natanfiuza.dev.br/post/show/devtools-ao-terminal-como-dissecar-gargalos-apis-curl-nativo)
- **A WinterCG e o Tratado de Paz dos Runtimes JavaScript** — Node, Deno y Bun caminaban hacia una guerra de APIs propietarias. WinterCG cambió el juego hacia una convergencia en torno a las Web APIs nativas. [Leer el artículo completo](https://tech-pulse.natanfiuza.dev.br/post/show/wintercg-e-tratado-paz-dos-runtimes-javascript)
- **O Manual Técnico do EU AI Act: Proteção Real ou Reserva de Mercado?** — ¿La regulación europea protege a la sociedad o funciona como barrera que asfixia a las startups y consolida a las Big Tech? [Leer el artículo completo](https://tech-pulse.natanfiuza.dev.br/post/show/manual-tecnico-eu-ai-act-protecao-real-ou-reserva-mercado)

> ¿Todavía no sigues el blog? Sigue las nuevas publicaciones en [tech-pulse.natanfiuza.dev.br](https://tech-pulse.natanfiuza.dev.br).

---

## 🔍 Curaduría de la semana

- **git-bisect — documentación oficial** — Después del artículo, vale la pena ver `git bisect run` con un script de test automatizado. [Leer la documentación](https://git-scm.com/docs/git-bisect)
- **WinterTC** — El grupo que nació como WinterCG y que hoy define la interoperabilidad entre runtimes de JavaScript. [Visitar el sitio](https://wintertc.org/)
- **EU AI Act — Regulatory Framework** — La página oficial de la Comisión Europea, con el calendario de aplicación por fases. [Leer el documento](https://digital-strategy.ec.europa.eu/en/policies/regulatory-framework-ai)

---

## 🛠️ Detrás del código

**Por qué el portugués no tiene archivo propio en el boletín**

Esta semana el boletín empezó a salir en el idioma de cada suscriptor. Vive en la columna `lang`, y el envío busca el cuerpo en `newsletter_body.{lang}.md`.

La decisión interesante fue dónde colocar el portugués. Lo obvio sería crear `newsletter_body.pt_br.md`, replicando los otros idiomas. No fue lo que hice.

El resolutor busca: el archivo del idioma, luego `newsletter_body.md`, luego `.pt_br.md` y `.en.md`. El base es el **segundo** de la fila — el fallback universal.

Si el portugués viviera en `pt_br.md`, un suscriptor con un idioma no soportado — `de`, `it` — caería en el `.en.md`. Manteniendo el pt-BR en la base, recibe portugués: el idioma del autor es el valor por defecto razonable para quien no tiene traducción.

El coste es que `body_exists()` tiene que ignorar el fallback para responder "¿esta edición tiene traducción dedicada?" — una línea más para no mentir sobre lo que existe en disco.

---

## 💬 Tu turno

¿Qué herramienta de tu stack usas desde hace años y aún descubres algo nuevo?

Responde a este correo. ¡Los mejores temas se convierten en tema de la próxima edición!

---

## 🔁 Antes de cerrar

Reenvíalo a ese colega dev que pasa el día cazando bugs en el historial de Git.

¡Nos vemos el próximo lunes!

— **Nataniel Fiuza**  
*Desarrollador de Software y Arquitecto de Soluciones*  
[tech-pulse.natanfiuza.dev.br](https://tech-pulse.natanfiuza.dev.br)
