# Tech Pulse Blog

## Descrição

O Tech Pulse Blog é um projeto de blog pessoal desenvolvido com o objetivo de demonstrar a integração entre Laravel, Inertia.js e Vue.js. Ele oferece funcionalidades básicas de um blog, como criação e visualização de posts, e serve como um ponto de partida para projetos mais complexos utilizando essa stack. O blog é responsivo e tem um design simples e limpo.

## Dependências Utilizadas

Este projeto utiliza as seguintes dependências principais:

*   **Backend:**
    *   [Laravel](https://laravel.com/) (v10 ou superior): Framework PHP robusto e elegante.
    *   [Inertia.js (Servidor)](https://inertiajs.com/):  Permite construir SPAs (Single Page Applications) com roteamento e controllers do lado do servidor.
    *   [spatie/laravel-medialibrary](https://spatie.be/docs/laravel-medialibrary/v10/introduction) (Opcional, mas recomendado para gestão de uploads).

*   **Frontend:**
    *   [Vue.js](https://vuejs.org/) (v3): Framework JavaScript progressivo para construir interfaces de usuário.
    *   [Inertia.js (Cliente)](https://inertiajs.com/):  Adapta o Vue.js para funcionar com o Inertia.js.
    *   [Vite](https://vitejs.dev/): Ferramenta de build extremamente rápida para o frontend.
    *   [`@vitejs/plugin-vue`](https://github.com/vitejs/vite-plugin-vue/tree/main/packages/plugin-vue): Plugin do Vite para suporte ao Vue.js.
    *   [`laravel-vite-plugin`](https://laravel.com/docs/10.x/vite): Plugin do Vite para integração com Laravel.
    *  [Tailwind CSS](https://tailwindcss.com/):  (Opcional) Framework CSS utilitário para estilização rápida (altamente recomendado).

* **Outros:**
    * PHP >= 8.1
    * Composer
    * Node.js >= 16.x (LTS)
    * npm >= 8.x
    * Um banco de dados (MySQL, PostgreSQL, SQLite, etc.)

## Instalação

Siga os passos abaixo para instalar e configurar o projeto em seu ambiente de desenvolvimento:

1.  **Clone o repositório:**

    ```bash
    git clone https://github.com/natanfiuza/tech-pulse-blog.git
    cd tech-pulse-blog
    ```

2.  **Instale as dependências do PHP (Composer):**

    ```bash
    composer install
    ```

3.  **Instale as dependências do JavaScript (npm):**

    ```bash
    npm install
    ```

4.  **Configure o ambiente:**

    *   Copie o arquivo `.env.example` para `.env`:

        ```bash
        cp .env.example .env
        ```
    *   Edite o arquivo `.env` e configure as variáveis de ambiente, principalmente as relacionadas ao banco de dados (DB_DATABASE, DB_USERNAME, DB_PASSWORD, etc.).
    *   Gere a chave da aplicação:
        ```bash
        php artisan key:generate
        ```

5. **Crie o banco de dados:**
    * Crie um banco de dados vazio no seu sistema gerenciador (ex. MySQL).

6.  **Execute as migrações (migrations):**

    ```bash
    php artisan migrate
    ```
    Isso criará as tabelas necessárias no banco de dados.

7.  **(Opcional) Execute os seeders:**
     Se você quiser popular o seu banco de dados com dados iniciais rode:
     ```bash
     php artisan db:seed
     ```

8.  **Compile os assets do frontend:**

    ```bash
    npm run dev
    ```
    Para desenvolvimento, use `npm run dev` para *hot reloading*.  Para produção, use `npm run build`.

9. **Inicie o servidor de desenvolvimento:**

   ```bash
    php artisan serve
   ```

10. **Acesse o blog no navegador:**

   O blog estará disponível em `http://localhost:8000` (ou na porta que você configurar).

## Utilização

*   **Criação de Posts:**  Foram feitas algumas implementações e agora você pode fazer o login e cadastrar um Post.
*   **Visualização de Posts:**  Na página inicial (`/`), você verá uma lista dos posts mais recentes.  Clique em um título para ver a página de detalhes do post.
*   **Login/Logout:** Use a rota `/login` para fazer login. A rota de logout é `/logout`.
*   **Usuários:** Para criar posts, é preciso ter usuários. O projeto não implementa o registro de usuários por padrão para simplificar. Você pode adicionar usuários manualmente através do `php artisan tinker`, ou criar uma rota de registro, seguindo os passos para criação de um blog completo.

## Considerações Finais e Agradecimentos

Este projeto foi desenvolvido como um exemplo prático da integração entre Laravel, Inertia.js e Vue.js.  Ele é um ponto de partida e pode ser expandido com diversas funcionalidades, como registro de usuários, edição de posts, comentários, categorias, tags, upload de imagens, etc.

Agradeço a todos que contribuírem para o meu aprendizado e desenvolvimento profissional.

## Contribuições e Contato

Contribuições são bem-vindas! Se você encontrar algum problema ou tiver sugestões de melhoria, sinta-se à vontade para abrir uma *issue* ou enviar um *pull request*.

**Contato:**

*   **Nome:** Natan Fiuza
*   **Email:** [contato@natanfiuza.dev.br](mailto:contato@natanfiuza.dev.br)

