# Tech Pulse Blog

Blog pessoal de tecnologia (Laravel + Inertia + Vue 3) que publica artigos sobre desenvolvimento, IA, gadgets e carreira, com área administrativa organizada por perfis de usuário (leitor, autor e admin).

## Language

### Conteúdo

**Post**:
Conteúdo publicado no blog escrito em markdown, com título, resumo (excerpt), imagem de capa e autor. Todo post gera uma URL pública única pelo slug.
_Avoid_: artigo (usar no plural do menu), postagem, story

**Destaque**:
Post em evidência exibido no topo da página principal. É selecionado automaticamente: o post mais recente é o destaque.
_Avoid_: featured, post fixado

### Classificação

**Categoria**:
Classificação hierárquica do post, organizada em até 3 níveis (categoria raiz → sub-categoria → nível 3), cada uma com descrição própria. Cada post pertence a exatamente uma categoria, escolhida no nível mais específico.
_Avoid_: tema, seção, assunto

**Sub-categoria**:
Categoria filha de outra, ligada via `parent_id`. Uma categoria com filhos é uma raiz para efeito de exibição nos filtros.

**Hashtag**:
Palavra-chave livre atribuída a um post, sem hierarquia. O autor pode digitar qualquer hashtag ou escolher entre sugestões das já existentes. Toda hashtag tem página própria listando seus posts.
_Avoid_: tag (no backend), palavra-chave, label

### Interação

**Comentário**:
Resposta de um usuário logado a um post, publicada imediatamente. Pode responder outro comentário, formando threads de profundidade ilimitada via `parent_id`.
_Avoid_: discussão, mensagem, reply

**Upvote**:
Voto de aprovação de um usuário logado sobre um comentário. Alternável: votar de novo remove o voto; um usuário pode ter no máximo um voto por comentário.
_Avoid_: like, curtida, avaliação

### Publicação

**Rascunho**:
Post salvo sem estar visível ao público. Só deixa de ser rascunho quando é publicado.
_Avoid_: draft, inédito

**Agendamento**:
Data e hora programada para um post ser publicado automaticamente. Quando definido, o post fica agendado (não rascunho, não publicado) até a data chegar.
_Avoid_: schedule, published_at

### Identificação

**Slug**:
Identificador de URL gerado a partir do título com transliteração para caracteres ASCII (sem acentos) e sem palavras irrelevantes (artigos e preposições). É único no blog; conflitos recebem sufixo numérico (`-2`, `-3`).
_Avoid_: url, path, permalink

### Pessoas

**Usuário**:
Pessoa com conta no blog, criada por cadastro self-service ou pelo login Google. Todo usuário tem um perfil (papel) que define o que pode fazer. Um usuário removido tem o acesso revogado, mas seu conteúdo permanece — o nome é substituído por "Usuário removido".
_Avoid_: account, membro, login

**Leitor**:
Perfil padrão de novos cadastros. Pode comentar, votar em comentários e consultar o dashboard pessoal (`/minha-conta`) com histórico de visualizações. Não acessa o painel admin.
_Avoid_: leitor/comentarista, visitante, seguidor

**Autor**:
Perfil concedido pelo admin a um leitor. Publica e gerencia os próprios posts no painel admin; não vê nem edita posts de outros autores.
_Avoid_: escritor, editor

**Admin**:
Perfil de administração total do blog: vê e gerencia todos os posts, categorias e usuários (concede e revoga perfis, remove usuários com soft delete). Os usuários iniciais do blog (seed) são admins.
_Avoid_: administrador (no código), root, superuser

**Visualização**:
Registro de que um usuário logado abriu um post, usado no histórico do dashboard do leitor. Só existe para usuários logados; o mesmo par usuário-post mantém apenas a última data.
_Avoid_: view, hit, acesso
