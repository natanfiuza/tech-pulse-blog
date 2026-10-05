# TechPulse Newsletter — Edición #01

## 👋 Apertura personal

¡Hola, dev!

Bienvenido(a) a la primera edición de **TechPulse**. Después de casi 30 años trabajando en desarrollo de sistemas — de Laravel, Vue y bases de datos relacionales hasta arquitectura de software y automatizaciones con inteligencia artificial — decidí crear este espacio para compartir de forma más directa lo que aprendo día a día.

La idea aquí no es ser otro boletín genérico con titulares sueltos. Es traer contenido técnico aplicable, buenas prácticas que puedas ejecutar en tu terminal o proyecto esta misma semana, y reflexiones sinceras sobre el ecosistema.

¡Vamos directo al grano!

---

## 💡 Consejo técnico de la semana

**Evita consultas N+1 en Eloquent con `preventLazyLoading`**

En desarrollo es común dejar pasar relaciones cargadas dentro de bucles (N+1). Para que eso no llegue nunca a producción, activa el bloqueo global en `AppServiceProvider`:

```php
public function boot(): void
{
    Model::preventLazyLoading(! app()->isProduction());
}
```

Con esa única línea, Laravel lanza una excepción inmediata al intentar hacer lazy loading de cualquier relación en entorno local o de pruebas, garantizando que uses `with()` o `loadMissing()`.

---

## 📝 Del blog TechPulse

Esto es lo que salió en el blog en las últimas semanas:

- **O PostgreSQL 19 Não Aposentou o pgvector** — PostgreSQL 19 no trajo tipos vectoriales ni índices HNSW al core. El titular real de la versión es REPACK — y cambia la vida de quien guarda embeddings en Postgres. [Leer el artículo completo](https://tech-pulse.natanfiuza.dev.br/post/show/postgresql-19-nao-aposentou-pgvector)
- **O PHP Tem Duas Datas de Morte, Não Uma** — El 31 de diciembre de 2026, PHP 8.2 llega al final de todo y PHP 8.4 sale del soporte activo. Dos fronteras en la misma fecha, y solo una de ellas es un abismo de verdad. [Leer el artículo completo](https://tech-pulse.natanfiuza.dev.br/post/show/php-tem-duas-datas-morte-nao)
- **O Domínio de Exemplo que Ninguém Comprou Ainda** — Quien escribe example.com usa un nombre que la RFC 2606 reservó en 1999; quien escribe yoursite.com usa un nombre que cualquiera puede comprar hoy. Unos 359 mil archivos en GitHub eligieron la segunda opción. [Leer el artículo completo](https://tech-pulse.natanfiuza.dev.br/post/show/dominio-exemplo-que-ninguem-comprou-ainda)
- **Autorregulação da IA: Quem É Auditado Escreve a Auditoria** — Tres competidores van a crear juntos el organismo que decide si su producto es seguro. El modelo citado es la FINRA — pero las dos cosas que le dan dientes a la FINRA quedaron fuera del diseño. [Leer el artículo completo](https://tech-pulse.natanfiuza.dev.br/post/show/autorregulacao-ia-quem-e-auditado-escreve-auditoria)

> ¿Todavía no sigues el blog? Sigue las nuevas publicaciones y tutoriales técnicos en [tech-pulse.natanfiuza.dev.br](https://tech-pulse.natanfiuza.dev.br).

---

## 🔍 Curaduría de la semana

Enlaces y lecturas que valieron mi tiempo estos días:

- **Laravel 11 Reverb & Serverless WebSockets** — Guía definitiva de websockets escalables y nativos en PHP. [Ver documentación](https://laravel.com/docs/11.x/reverb)
- **Vite 6 & The Future of Frontend Tooling** — Qué cambia con la nueva versión del bundler más rápido de la web. [Ver anuncio](https://vite.dev/blog)
- **RFC 8058: One-Click Unsubscribe** — Cómo los grandes proveedores (Gmail/Yahoo) priorizan la entregabilidad del correo conforme a la normativa. [Leer RFC](https://datatracker.ietf.org/doc/html/rfc8058)

---

## 🛠️ Detrás del código

**El día en que un UUID salvó nuestra integridad de datos**

Durante la refactorización de nuestro sistema de subida y servido de imágenes incrustadas en el editor Markdown, nos topamos con el reto del slug mutable: cuando el autor cambiaba el título del post, todos los enlaces de las imágenes antiguas se rompían.

La solución fue separar el identificador público de la entidad de su representación visual: el `{slug}` en la ruta es meramente decorativo para SEO, mientras que el backend resuelve y limpia los archivos únicamente por el `{uuid}` de 36 caracteres. Simplicidad arquitectónica que previene errores silenciosos.

---

## 💬 Tu turno

¿Usas IA en tu día a día de desarrollo — y en qué momento desconfías de ella?

Responde directamente a este correo. Leo todas las respuestas personalmente y los mejores temas se convierten en artículos del blog y en tema de la próxima edición.

---

## 🔁 Antes de cerrar

Si este contenido te resultó útil, compártelo con un colega dev. Ayuda muchísimo a que la comunidad TechPulse crezca.

¡Nos vemos el próximo lunes!

— **Nataniel Fiuza**  
*Desarrollador de Software y Arquitecto de Soluciones*  
[tech-pulse.natanfiuza.dev.br](https://tech-pulse.natanfiuza.dev.br)
