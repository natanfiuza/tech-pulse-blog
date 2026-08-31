<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Semeia a hierarquia de categorias do doc/estrutura/categorias.md
     * (15 raízes + 41 sub-categorias + 6 itens de nível 3).
     *
     * Idempotente via updateOrCreate por slug. Nota: updateOrCreate
     * sobrescreve descrições editadas manualmente no admin.
     *
     * @var array<int, array<string, string|null>>
     */
    private array $categorias = [
        // ---- I. Tecnologia em Geral ----
        ['name' => 'Notícias e Tendências', 'slug' => null, 'parent_slug' => null, 'description' => 'Novidades, tendências emergentes, eventos e análise de mercado de tecnologia.'],
        ['name' => 'Novidades', 'slug' => null, 'parent_slug' => 'noticias-e-tendencias', 'description' => 'Lançamentos de produtos, atualizações de software, aquisições de empresas, etc.'],
        ['name' => 'Tendências Emergentes', 'slug' => null, 'parent_slug' => 'noticias-e-tendencias', 'description' => 'Exploração de tecnologias promissoras como metaverso, computação quântica, Web3, biotecnologia, etc.'],
        ['name' => 'Eventos', 'slug' => null, 'parent_slug' => 'noticias-e-tendencias', 'description' => 'Cobertura de feiras, conferências e workshops (CES, WWDC, Google I/O, etc.).'],
        ['name' => 'Análise de Mercado', 'slug' => null, 'parent_slug' => 'noticias-e-tendencias', 'description' => 'Discussões sobre o estado atual e futuro do mercado de tecnologia.'],

        ['name' => 'Gadgets e Dispositivos', 'slug' => null, 'parent_slug' => null, 'description' => 'Reviews, guias e tutoriais sobre smartphones, computadores, wearables, casa inteligente e outros gadgets.'],
        ['name' => 'Smartphones e Tablets', 'slug' => null, 'parent_slug' => 'gadgets-e-dispositivos', 'description' => 'Reviews, comparações, dicas e tutoriais.'],
        ['name' => 'Computadores e Laptops', 'slug' => null, 'parent_slug' => 'gadgets-e-dispositivos', 'description' => 'Reviews, guias de compra, otimização de desempenho.'],
        ['name' => 'Wearables', 'slug' => null, 'parent_slug' => 'gadgets-e-dispositivos', 'description' => 'Smartwatches, fitness trackers, óculos inteligentes, etc.'],
        ['name' => 'Casa Inteligente', 'slug' => null, 'parent_slug' => 'gadgets-e-dispositivos', 'description' => 'Automação residencial, dispositivos IoT, assistentes virtuais.'],
        ['name' => 'Outros Gadgets', 'slug' => null, 'parent_slug' => 'gadgets-e-dispositivos', 'description' => 'Drones, câmeras, fones de ouvido, etc.'],

        ['name' => 'Software e Aplicativos', 'slug' => null, 'parent_slug' => null, 'description' => 'Sistemas operacionais, aplicativos móveis, softwares para desktop e serviços web.'],
        ['name' => 'Sistemas Operacionais', 'slug' => null, 'parent_slug' => 'software-e-aplicativos', 'description' => 'Notícias, dicas e tutoriais sobre Windows, macOS, Linux, iOS, Android.'],
        ['name' => 'Aplicativos Móveis', 'slug' => null, 'parent_slug' => 'software-e-aplicativos', 'description' => 'Reviews, recomendações e guias de uso.'],
        ['name' => 'Softwares para Desktop', 'slug' => null, 'parent_slug' => 'software-e-aplicativos', 'description' => 'Produtividade, edição de imagem/vídeo, segurança, etc.'],
        ['name' => 'Serviços Web', 'slug' => null, 'parent_slug' => 'software-e-aplicativos', 'description' => 'Plataformas de streaming, armazenamento em nuvem, ferramentas online.'],

        ['name' => 'Inteligência Artificial (IA) e Machine Learning', 'slug' => null, 'parent_slug' => null, 'description' => 'Conceitos, aplicações, ética e ferramentas de IA e machine learning.'],
        ['name' => 'Conceitos de IA', 'slug' => null, 'parent_slug' => 'inteligencia-artificial-ia-e-machine-learning', 'description' => 'Explicações acessíveis sobre os fundamentos da IA.'],
        ['name' => 'Aplicações de IA', 'slug' => null, 'parent_slug' => 'inteligencia-artificial-ia-e-machine-learning', 'description' => 'Exemplos práticos de IA em diversos setores.'],
        ['name' => 'Ética e IA', 'slug' => null, 'parent_slug' => 'inteligencia-artificial-ia-e-machine-learning', 'description' => 'Discussões sobre os impactos sociais e éticos da IA.'],
        ['name' => 'Ferramentas e Plataformas', 'slug' => null, 'parent_slug' => 'inteligencia-artificial-ia-e-machine-learning', 'description' => 'Introdução a frameworks de Machine Learning (TensorFlow, PyTorch, etc.).'],

        ['name' => 'Segurança e Privacidade', 'slug' => null, 'parent_slug' => null, 'description' => 'Cibersegurança, privacidade online e criptografia.'],
        ['name' => 'Cibersegurança', 'slug' => null, 'parent_slug' => 'seguranca-e-privacidade', 'description' => 'Notícias sobre ameaças, dicas de proteção, melhores práticas.'],
        ['name' => 'Privacidade Online', 'slug' => null, 'parent_slug' => 'seguranca-e-privacidade', 'description' => 'Proteção de dados, navegação segura, VPNs.'],
        ['name' => 'Criptografia', 'slug' => null, 'parent_slug' => 'seguranca-e-privacidade', 'description' => 'Conceitos básicos, aplicações e importância.'],

        // ---- II. Desenvolvimento de Sistemas e Linguagens ----
        ['name' => 'Linguagens de Programação', 'slug' => null, 'parent_slug' => null, 'description' => 'Guias, tutoriais, comparativos e boas práticas de linguagens de programação.'],
        ['name' => 'Guias e Tutoriais', 'slug' => null, 'parent_slug' => 'linguagens-programacao', 'description' => 'Guias e tutoriais de linguagens de programação.'],
        ['name' => 'Python', 'slug' => null, 'parent_slug' => 'guias-e-tutoriais', 'description' => 'Para iniciantes e avançados, com foco em aplicações específicas (web, data science, etc.).'],
        ['name' => 'JavaScript', 'slug' => null, 'parent_slug' => 'guias-e-tutoriais', 'description' => 'Front-end (React, Angular, Vue.js), back-end (Node.js), desenvolvimento mobile (React Native).'],
        ['name' => 'Java', 'slug' => null, 'parent_slug' => 'guias-e-tutoriais', 'description' => 'Desenvolvimento corporativo, aplicações Android.'],
        ['name' => 'C#', 'slug' => 'c-sharp', 'parent_slug' => 'guias-e-tutoriais', 'description' => 'Desenvolvimento de jogos (Unity), aplicações Windows.'],
        ['name' => 'C++', 'slug' => 'cpp', 'parent_slug' => 'guias-e-tutoriais', 'description' => 'Programação de sistemas, jogos, alto desempenho.'],
        ['name' => 'Outras Linguagens', 'slug' => null, 'parent_slug' => 'guias-e-tutoriais', 'description' => 'Go, Rust, Swift, Kotlin, PHP, Ruby, etc. (Conforme a demanda e especialização do blog).'],
        ['name' => 'Comparativos e Escolhas', 'slug' => null, 'parent_slug' => 'linguagens-programacao', 'description' => 'Qual linguagem aprender para cada objetivo.'],
        ['name' => 'Melhores Práticas e Padrões de Projeto', 'slug' => null, 'parent_slug' => 'linguagens-programacao', 'description' => 'Estilo de código, design patterns, arquitetura de software.'],

        ['name' => 'Desenvolvimento Web', 'slug' => null, 'parent_slug' => null, 'description' => 'Front-end, back-end, bancos de dados, DevOps e hospedagem.'],
        ['name' => 'Front-end', 'slug' => null, 'parent_slug' => 'desenvolvimento-web', 'description' => 'HTML, CSS, JavaScript, frameworks (React, Angular, Vue.js), responsividade, acessibilidade.'],
        ['name' => 'Back-end', 'slug' => null, 'parent_slug' => 'desenvolvimento-web', 'description' => 'Node.js, Python (Django, Flask), Ruby on Rails, PHP (Laravel), APIs RESTful.'],
        ['name' => 'Bancos de Dados', 'slug' => null, 'parent_slug' => 'desenvolvimento-web', 'description' => 'SQL (MySQL, PostgreSQL), NoSQL (MongoDB, Cassandra).'],
        ['name' => 'DevOps', 'slug' => null, 'parent_slug' => 'desenvolvimento-web', 'description' => 'Automação, integração contínua (CI), entrega contínua (CD), conteinerização (Docker, Kubernetes).'],
        ['name' => 'Hospedagem e Servidores', 'slug' => null, 'parent_slug' => 'desenvolvimento-web', 'description' => 'AWS, Google Cloud, Azure, Heroku, etc.'],

        ['name' => 'Desenvolvimento Mobile', 'slug' => null, 'parent_slug' => null, 'description' => 'Android, iOS e desenvolvimento híbrido.'],
        ['name' => 'Android', 'slug' => null, 'parent_slug' => 'desenvolvimento-mobile', 'description' => 'Java, Kotlin, Android Studio, Jetpack Compose.'],
        ['name' => 'iOS', 'slug' => null, 'parent_slug' => 'desenvolvimento-mobile', 'description' => 'Swift, Objective-C, Xcode, SwiftUI.'],
        ['name' => 'Desenvolvimento Híbrido', 'slug' => null, 'parent_slug' => 'desenvolvimento-mobile', 'description' => 'React Native, Flutter, Ionic.'],

        ['name' => 'Desenvolvimento de Jogos', 'slug' => null, 'parent_slug' => null, 'description' => 'Motores, linguagens e design de jogos.'],
        ['name' => 'Motores de Jogos', 'slug' => null, 'parent_slug' => 'desenvolvimento-jogos', 'description' => 'Unity, Unreal Engine.'],
        ['name' => 'Linguagens', 'slug' => null, 'parent_slug' => 'desenvolvimento-jogos', 'description' => 'C#, C++.'],
        ['name' => 'Design de Jogos', 'slug' => null, 'parent_slug' => 'desenvolvimento-jogos', 'description' => 'Conceitos de game design, level design, narrativa.'],

        ['name' => 'Ciência de Dados e Análise de Dados', 'slug' => null, 'parent_slug' => null, 'description' => 'Bibliotecas, visualização, estatística e modelagem de dados.'],
        ['name' => 'Bibliotecas e Ferramentas', 'slug' => null, 'parent_slug' => 'ciencia-dados-e-analise-dados', 'description' => 'Pandas, NumPy, Scikit-learn, Jupyter Notebooks.'],
        ['name' => 'Visualização de Dados', 'slug' => null, 'parent_slug' => 'ciencia-dados-e-analise-dados', 'description' => 'Matplotlib, Seaborn, Plotly.'],
        ['name' => 'Estatística e Modelagem', 'slug' => null, 'parent_slug' => 'ciencia-dados-e-analise-dados', 'description' => 'Conceitos estatísticos aplicados à análise de dados.'],

        ['name' => 'Carreira e Desenvolvimento Profissional', 'slug' => null, 'parent_slug' => null, 'description' => 'Dicas para iniciantes, portfólio, entrevistas técnicas e mercado de trabalho.'],
        ['name' => 'Dicas para Iniciantes', 'slug' => null, 'parent_slug' => 'carreira-e-desenvolvimento-profissional', 'description' => 'Como começar na área de programação.'],
        ['name' => 'Construindo um Portfólio', 'slug' => null, 'parent_slug' => 'carreira-e-desenvolvimento-profissional', 'description' => 'Projetos pessoais, contribuições open source.'],
        ['name' => 'Entrevistas Técnicas', 'slug' => null, 'parent_slug' => 'carreira-e-desenvolvimento-profissional', 'description' => 'Preparação para entrevistas de emprego.'],
        ['name' => 'Mercado de Trabalho', 'slug' => null, 'parent_slug' => 'carreira-e-desenvolvimento-profissional', 'description' => 'Tendências de contratação, salários, habilidades demandadas.'],

        // ---- III. Outras Categorias Relevantes ----
        ['name' => 'Tutoriais e Guias', 'slug' => null, 'parent_slug' => null, 'description' => '"Como fazer" em diversas áreas de tecnologia e programação.'],
        ['name' => 'Reviews', 'slug' => null, 'parent_slug' => null, 'description' => 'Análises aprofundadas de produtos, softwares e serviços.'],
        ['name' => 'Opinião', 'slug' => null, 'parent_slug' => null, 'description' => 'Artigos de opinião sobre temas relevantes do mundo tecnológico.'],
        ['name' => 'Entrevistas', 'slug' => null, 'parent_slug' => null, 'description' => 'Conversas com profissionais e personalidades da área.'],
    ];

    /**
     * Executa o seeder.
     */
    public function run(): void
    {
        foreach ($this->categorias as $categoria) {
            $slug = $categoria['slug'] ?? criar_slug($categoria['name']);

            Category::updateOrCreate(
                ['slug' => $slug],
                [
                    'name' => $categoria['name'],
                    'description' => $categoria['description'],
                    'parent_id' => $categoria['parent_slug']
                        ? Category::where('slug', $categoria['parent_slug'])->value('id')
                        : null,
                ]
            );
        }
    }
}
