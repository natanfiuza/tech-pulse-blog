# 🚀 Sugestão de Pull Request

**Mensagem de Commit Recomendada:**
```text
feat: adiciona imagens de conteúdo no editor de posts
```

---

## 🎯 Resumo

O editor de posts permitia texto e código, mas não imagens: para ilustrar um artigo era preciso hospedar o arquivo fora e colar a URL na mão. Este PR resolve o ciclo completo de imagens embutidas no conteúdo — colar, arrastar ou escolher um arquivo no editor insere a imagem no ponto do cursor, e o arquivo é servido pelo próprio blog.

O ponto central da decisão é que a verdade sobre quais imagens um post usa é o **próprio markdown**, e não uma tabela de registro. Isso mantém o modelo de dados intacto e faz a limpeza emergir de um diff entre o texto antigo e o novo a cada save. Duas consequências guiaram o desenho: o upload precisa funcionar **antes de o post existir** (o autor cola a imagem enquanto escreve) e **renomear um post não pode quebrar imagens já gravadas** — por isso a URL carrega um slug decorativo e a leitura resolve o arquivo apenas pelo uuid.

## 🛠️ Mudanças Técnicas

- **`ContentImageController`**: recebe o upload imediato (`store`, 201 com `uuid` e URL absoluta) e serve o arquivo por uuid (`show`), com validação de uuid antes de tocar no disco e `Cache-Control: immutable` justificado pela imutabilidade do caminho.
- **`ImagensDeConteudo` (service)**: concentra caminho, disco, montagem da URL absoluta, extração de uuids referenciados no markdown e a limpeza (`sincronizar`, `remover_todas`). A extração casa pelo caminho da rota, nunca pelo domínio, tolerando URLs absolutas, relativas ou de domínios antigos.
- **`PostController`**: guarda o conteúdo anterior antes do update e sincroniza depois do save; no destroy, remove as imagens do post após o delete e preserva as que outro post ainda referencia.
- **`config/techpulse.php`**: introduz `url_publica` e `imagens_conteudo`, com aviso explícito para não usar `config('app.url')` (vazaria o domínio de desenvolvimento para o banco).
- **`MarkdownEditor.vue`**: handlers próprios de paste/drop no CodeMirror, seletor de arquivo dedicado, validação de tipo/tamanho com feedback e envio **sequencial** para preservar a ordem das imagens no texto.
- **`helpers.js` + `Post.vue`**: `normalizar_origem_conteudo()` troca a origem de produção pela do navegador na renderização, aplicada na regra do token de imagem (não no markdown, para não afetar blocos de código).
- **Rotas**: `GET /post/content/images/{slug}/{uuid}` pública e `POST /content-images` no grupo autenticado de autor/admin.
- **Testes**: cobertura de upload, limites, autorização, entrega, 404s e cenários de limpeza (imagem removida do texto, compartilhada entre posts, destroy).

## ⚠️ Impacto/Avisos

- **Nova variável de ambiente**: `TECHPULSE_URL_PUBLICA`, com fallback para o domínio de produção. É o prefixo gravado nas URLs do markdown e a base da troca de origem no frontend — sem ela correta, as imagens quebram em produção.
- **Banco de dados**: nenhuma migração ou alteração de schema. As imagens convivem com o markdown existente; posts sem imagem de conteúdo não são afetados.
- **Dependência (dev)**: `doctrine/dbal` adicionada em `require-dev` para suportar alterações de coluna em SQLite — não vai para produção.
- **Ambiente de testes**: `phpunit.xml` passa a usar SQLite `:memory:` e `ExampleTest` adota `RefreshDatabase`. A suíte agora depende de banco isolado para rodar.
- **Contrato de URL**: a URL das imagens é absoluta e imutável por uuid. **Não** copiar o header `Cache-Control: immutable` para o `ImageController` de capa, que sobrescreve o mesmo caminho no re-upload.
- **Consumo externo**: a API pública passa a expor conteúdo com URLs absolutas do domínio de produção, formato já esperado pelo app Flutter.