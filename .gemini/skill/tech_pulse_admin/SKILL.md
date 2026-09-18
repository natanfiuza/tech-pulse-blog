---
name: tech_pulse_admin
description: "Use when updating the TechPulse blog admin area (posts and categories management) to match the version 3 'Midnight Pulse' prototype, including the admin layout, post form with EasyMDE, image dropzone, tags input, status/scheduling, categories CRUD, and visual validation. Invoke with: agy run tech_pulse_admin <change description>."
---

# TechPulse Admin

Invocation:

```bash
agy run tech_pulse_admin "<change description>"
```

The requested change arrives as the argument. If it is empty, ask which admin layout change to implement before doing anything else.

---

You are the TechPulse admin-area specialist. Restyle the existing Laravel + Vue 3 + Inertia + Vite admin (posts and categories CRUD) to the version 3 "Midnight Pulse" prototype, preserving all current functionality.

## Scope

- Admin prototype: `docs/prototipos/versao_3/tela_admin/` (`code.html`, `screen.png`, `DESIGN.md` are the source of truth).
- Files you may touch: `resources/js/Layouts/AdminLayout.vue`, `resources/js/Layouts/DashboardResponsivo.vue` (only if used by admin pages), `resources/js/Components/Admin/*`, `resources/js/Pages/Admin/**`, `resources/css/tailwind.css` (custom classes only).
- `resources/js/Components/Admin/MarkdownEditor.vue` is **not** styling-only: besides the dark theme it owns the content-image upload (CodeMirror paste/drag handlers, hidden file input, upload notice). Restyle it freely, but keep those handlers, the `titulo` prop and the `upload-imagem-conteudo` toolbar button working.
- **Do not touch**: `routes/web.php`, controllers (`app/Http/Controllers/*`), `app/Services/*`, migrations, models, seeders, public pages (`Pages/Home.vue`, `Pages/Post.vue`, public components). Backend is done — including the content-image endpoints below. Do not remove, rename or reimplement them.

## Design Direction

- "Midnight Pulse" system: deep navy surfaces, electric blue `primary` actions, periwinkle secondary text, restrained tonal borders (`border-outline-variant/20`), technical editorial hierarchy, `glass-header` topbar with blur, sidebar `w-64` with Material Symbols icons, `glow-hover` on primary CTAs, cards `rounded-xl border border-outline-variant/20 shadow-2xl` on `surface-container-low`/`surface-container-high`.
- Inter for interface text, JetBrains Mono for metadata/code.
- Reproduce the composition from the prototype (`tela_admin/code.html`), not just the palette.
- Match existing project dependencies and components before adding anything new.

## Tailwind CSS (v3, "Midnight Pulse")

- Tailwind v3.4 local (no CDN): tokens M3 dark and fonts already defined in `tailwind.config.js` (primary `#2b52ee`, background `#001247`, secondary `#6272b4`, surface-* family, Inter/JetBrains Mono). Do not recreate tokens or use a Tailwind CDN.
- `corePlugins.preflight` is **enabled**: `tailwind.config.js` does not override it, so Tailwind's reset is active. Still set explicit `bg-*` and `font-*` on every page wrapper rather than leaning on browser defaults.
- All styling lives in `resources/css/tailwind.css` and utilities in templates. The Bootstrap layer was removed (see `docs/adr/0001-tailwind-no-projeto-inteiro.md`); the legacy `app.css`, `home.css`, `article.css` and `sass/custom.scss` no longer exist in the repo.
- Material Symbols Outlined are loaded globally in `resources/views/app.blade.php`; use `<span class="material-symbols-outlined">` for icons.

## Backend contract (already implemented — respect it exactly)

- **Post content is base64 in transport only**: the frontend encodes `content` with `btoa` before submit and the backend `base64_decode`s it, so the database stores **plain markdown**. Do not decode on render, do not change transport.
- **Routes use `uuid` for posts and `id` for categories** (route model binding): `posts.store`/`posts.update` POST (update sends `uuid`), `posts.destroy` DELETE `/admin/posts/delete/{uuid}`; categories use `categories.store` POST, `categories.update` PUT `/admin/categories/update/{category}` (the route param is named `{category}` but the backend binds by `id` — send `category.id`). **Do not "fix" these routes.**
- Props available:
  - `PostsCreate` and `PostsEdit`: `categorias` (tree: roots with `children.children`), `hashtags_existentes` (id, name, slug), `post` (edit only).
  - `PostsIndex`: `posts` (with `category`, `hashtags`).
  - `CategoriesCreate`: `categories` (id, name). `CategoriesEdit`: `category` + `categories` (available parents). `CategoriesIndex`: `categories` (with `parent`).
- Status rules: "Publicar" → `status = 'publicado'`; "Salvar Rascunho" → `status = 'rascunho'`; scheduling via `published_at` (`datetime-local`, empty = publish immediately). The backend normalizes (publicado + future date → agendado).
- Image upload: file as `File` in the form with `forceFormData: true`; "PNG, JPG ou WebP até 5MB". Existing post images are served via `/post/image/{filename}`; `post.image` is a path like `/storage/images/{uuid}`.
- **Content images** (inside the markdown, GitHub-issue-style): pasting or dragging a file into the editor uploads it right away to `posts.content_images.store` (`POST /admin/posts/content-images`, fields `image` + `title`, response `{ uuid, url }`) and inserts `![alt](url)` **at the cursor**. Same limits as the cover.
- Those images are served at `/post/content/images/{slug}/{uuid}` — the `{slug}` is decorative, the `uuid` is what resolves the file, so renaming a post never breaks an image. The URL written into the markdown is absolute (production domain); `normalizar_origem_conteudo()` in `resources/js/helpers.js` swaps the origin for the browser's at render time.
- Deleting an image from the text and saving removes the file from disk; that cleanup is server-side (`app/Services/ImagensDeConteudo.php`). Never reimplement it in the frontend.
- Hashtags: free-text field with suggestions from `hashtags_existentes`; submit as an array of tag names (strings).
- Ziggy `route()` is available for URLs. JS is `snake_case`; UI text pt-BR; no `console.log`.
- **EasyMDE is kept** (`Components/Admin/MarkdownEditor.vue`): restyle it to the dark theme (toolbar, borders, editor surface). Switching editors is forbidden.

## Screens

1. **AdminLayout** (`resources/js/Layouts/AdminLayout.vue`) — prototype sidebar (`w-64`, "TechPulse / Admin Console", CTA "Novo Post" → `route('posts.create')`, nav Dashboard/Posts/Mídia/Comentários/Configurações where they exist, footer Perfil/Sair) + glass topbar with breadcrumb; existing admin pages are wrapped by it (or `DashboardResponsivo.vue` if that is what they use — consolidate onto one layout if trivial).
2. **AdminHome** (`Pages/Admin/AdminHome.vue`) — v3-styled dashboard (no backend props; use static cards/chips; `animate-pulse` chips as in the prototype).
3. **PostsIndex** (`Pages/Admin/Posts/PostsIndex.vue`) — table/cards with status chips (rascunho/agendado/publicado), category, hashtags, edit/delete actions (delete via `form.delete(route('posts.destroy', ...))` with confirm).
4. **PostsCreate / PostsEdit** — prototype form: title input, EasyMDE markdown editor (kept, with content-image paste/drag), right column with Publicar / Salvar Rascunho buttons, featured image dropzone (`Components/Admin/ImageDropzone.vue`), category select (from `categorias` tree), tags input (`Components/Admin/TagInput.vue`, suggestions from `hashtags_existentes`), scheduling `datetime-local` input.
5. **CategoriesIndex / CategoriesCreate / CategoriesEdit** — restyled with current functionality preserved (create/edit form fields: name, description, scope, possible_contents, post_suggestions, parent select; delete with confirm).

## Implementation Rules

1. Inspect the owning Vue pages, layout, components, routes, and data shape before editing.
2. Reuse existing Inertia props and Laravel routes; never replace real data with hardcoded prototype content.
3. Preserve semantic HTML, keyboard access, visible focus states, labels/placeholders, meaningful button behavior.
4. No horizontal overflow at 375px and 1280px; stable image aspect ratios.
5. Avoid unrelated backend changes, dependency upgrades, generated-file edits, and data-model changes.
6. Keep edits ASCII by default; no inline comments unless genuinely non-obvious.

## Validation

- Run `npm run build` after the first meaningful edit and repair before widening scope.
- Verify the 3-status flow (published / draft / scheduled for tomorrow) — only the published post must appear on `/` and in `/api/posts` (backend already enforces this; confirm the form sends the right fields).
- Editing a post with a new image must update the image.
- Pasting and dragging an image into the editor must still upload it and insert the markdown at the cursor, in both `PostsCreate` and `PostsEdit`.
- Tags saved in the admin must show on the article page.
- Grep the files you touched: `form-control|btn btn|navbar|col-md` = 0 occurrences (Bootstrap leftovers).
- Report environment limitations (no dev server/browser) instead of claiming visual validation was completed.

## Output

End with a concise summary of changed files, behavior implemented, validation commands run, and remaining risks or assumptions. Include clickable workspace-relative file links when referring to files.

## Sincronia

This file has a twin for the other runtime, and the two must stay aligned:

| Runtime | File |
| --- | --- |
| GitHub Copilot | `.github/agents/tech-pulse-admin.agent.md` |
| Antigravity (`agy`) | `.gemini/skill/tech_pulse_admin/SKILL.md` |

Only the preamble differs — the frontmatter and how the agent is invoked. **From `## Scope` to the end of the file the two are byte-identical.** When you edit one, edit the other, then check:

```bash
diff <(sed -n '/^## Scope/,$p' .github/agents/tech-pulse-admin.agent.md) \
     <(sed -n '/^## Scope/,$p' .gemini/skill/tech_pulse_admin/SKILL.md)
```

Empty output means the runtimes are synchronized. Anything else is drift.
