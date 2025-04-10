# Discursivamente

Discursivamente é uma plataforma de biblioteca virtual desenvolvida em PHP, que integra funcionalidades avançadas para oferecer uma experiência rica e interativa. Com foco em escalabilidade, segurança e profissionalismo, o projeto reúne ferramentas como gerenciamento de livros, fóruns de discussão, clubes de leitura e controle de metas, facilitando o acesso e a interação dos usuários com o universo literário.

## Funcionalidades

- **Banners Promocionais:** Navegue por banners interativos com links para páginas de detalhes.
- **Categorias Principais:** Exibição de 10 categorias com subcategorias, com efeito de rolagem automática.
- **Funcionalidades da Plataforma:** Recursos que incluem fóruns, clubes de leitura, gerenciamento de metas e sugestões personalizadas.
- **Comentários e Feedbacks:** Seção dedicada à exibição de opiniões dos usuários.
- **Novidades:** Atualizações, eventos e lançamentos relacionados à plataforma.
- **Sobre a Plataforma:** Informações institucionais sobre a Biblioteca Virtual.
- **Contato:** Formulário para envio de mensagens e sugestões.
- **Livros:** Listagem de livros com links que direcionam para páginas de detalhes.
- **Autores Destaques:** Exibição de autores com foto e nome, com links para seus perfis.

## Tecnologias Utilizadas

- **Linguagem:** PHP
- **Frontend:** HTML, JavaScript (sem CSS neste repositório)
- **Controle de Versão:** Git

## Instalação

1. **Clone o repositório:**

   ```bash
   git clone <https://github.com/Marceloznnn/discursivamente.git>

## indentidade visual

 -**indentidade visual do site:**

 Tipografia
Duas Famílias de Fontes:
O código utiliza duas fontes provenientes do Google Fonts:

Playfair Display: Empregada nos títulos e elementos de destaque, transmite elegância e sofisticação.

Cormorant Garamond: Utilizada para os textos corridos e descrições, reforçando uma leitura fluida e um ar clássico.

Essa combinação cria um contraste harmonioso, onde os títulos ganham força e os parágrafos mantêm uma legibilidade refinada.

Paleta de Cores
Cores Neutras e Terrosas:

O fundo geral (#f5f5f5) e o fundo das seções garantem uma base neutra que valoriza os demais elementos.

Os tons de marrom e dourado, como o #8C7054 e o #3c2f1f, são usados para detalhes e destaques, reforçando a ideia de tradição e qualidade.

Contraste no Banner:

O banner utiliza uma imagem de fundo sobreposta por um gradiente escuro (rgba(0, 0, 0, 0.7)), o que não só valoriza os textos em branco, mas também cria um clima dramático e convidativo.

Layout e Estrutura
Seção do Banner:

A área principal é composta por um banner de 500px de altura, onde o conteúdo centralizado (título, subtítulo e formulário de pesquisa) é realçado por uma sobreposição escura.

A disposição centralizada e a ausência de bordas arredondadas conferem um aspecto moderno e direto.

Seção “Sobre Nós”:

Estruturada em duas colunas (grade), onde à esquerda é exibida uma imagem ilustrativa e à direita o conteúdo textual.

A divisão em grid cria um equilíbrio visual entre imagem e texto, promovendo clareza e organização.

A adição de um sublinhado decorativo abaixo do título "Sobre Nós" reforça o destaque visual e a identidade do projeto.

Componentes Interativos:

O formulário de busca no banner e os botões nas seções foram estilizados para responder a interações do usuário (hover), melhorando a experiência e reforçando a identidade de modernidade.

Responsividade e Acessibilidade
Design Responsivo:

São utilizadas media queries para ajustar o tamanho dos textos, o layout da grade e a disposição dos componentes, garantindo que o site se adapte bem a diferentes dispositivos (desktop, tablet e smartphone).

Clareza e Consistência Visual:

O uso consistente das fontes e paleta de cores, aliado a espaçamentos bem definidos (margens e paddings) e à organização em grid, resulta em uma identidade visual profissional e coesa.


DISCURSIVAMENTE/
├── cache/
├── logs/
├── private/
├── public/
│   ├── css/
│   │   ├── comunidade.css (U)
│   │   ├── forum.css (U)
│   │   ├── home.css (U)
│   │   ├── patials.css (U)
│   │   ├── perfil.css (U)
│   │   ├── register.css (U)
│   │   └── style.css (U)
│   ├── images/
│   ├── js/
│   ├── .htaccess (U)
│   ├── android-chrome-192x192.png (U)
│   ├── android-chrome-512x512.png (U)
│   ├── apple-touch-icon.png (U)
│   ├── favicon-16x16.png (U)
│   ├── favicon-32x32.png (U)
│   ├── favicon.ico (U)
│   ├── index.php (U)
│   ├── service-worker.js (U)
│   └── site.webmanifest (U)
src/
├── config/
│   ├── app.php (U)
│   └── database.php (U)
├── controllers/
│   ├── BibliotecaController.php (U)
│   ├── CommunityController.php (U)
│   ├── CommunityPageController.php (U)
│   ├── CompromisoController.php (U)
│   ├── HomeController.php (U)
│   ├── LoginController.php (U)
│   ├── LogoutController.php (U)
│   ├── OfflineController.php (U)
│   ├── PerfilController.php (U)
│   ├── QuemSomosController.php (U)
│   ├── RegisterController.php (U)
│   └── SearchController.php (U)
├── models/
├── pages/
├── routes/
│   └── web.php (U)
├── tests/
└── views/
    └── compromiso/

DISCURSIVAMENTE/
├── config/
│   ├── app.php
│   ├── database.php
│   └── mail.php                     # Adicionado, pois não apareceu no print
├── public/
│   ├── assets/
│   │   ├── css/
│   │   │   ├── components/
│   │   │   │   ├── buttons.css
│   │   │   │   ├── cards.css
│   │   │   │   ├── forms.css
│   │   │   │   └── navigation.css
│   │   │   ├── pages/
│   │   │   │   ├── comunidade.css
│   │   │   │   ├── forum.css
│   │   │   │   ├── home.css
│   │   │   │   ├── perfil.css
│   │   │   │   └── register.css
│   │   │   └── style.css
│   │   ├── images/
│   │   │   ├── icons/
│   │   │   ├── backgrounds/
│   │   │   └── uploads/
│   │   ├── js/
│   │   │   ├── components/
│   │   │   ├── pages/
│   │   │   └── app.js
│   │   └── fonts/
│   ├── favicon.ico
│   ├── robots.txt
│   ├── manifest.json
│   └── index.php
├── src/
│   ├── Controllers/
│   │   ├── Auth/
│   │   │   ├── LoginController.php       # Se já existir
│   │   │   ├── RegisterController.php    # Se já existir
│   │   │   └── LogoutController.php      # Se já existir
│   │   ├── Community/
│   │   │   ├── CommunityController.php   # Ex-CommunidadeController.php
│   │   │   ├── ForumController.php       # Ex-ForunsController.php (se houver)
│   │   │   ├── EventController.php       # Se existir
│   │   │   ├── BibliotecaController.php  # Se a "Biblioteca" fizer parte da Comunidade
│   │   │   └── CompromissoController.php # Se "Compromisso" for parte de algo comunitário
│   │   ├── Profile/
│   │   │   ├── ProfileController.php     # Ex-PerfilController.php
│   │   │   └── BibliotecaController.php  # Se a "Biblioteca" for parte do perfil do usuário 
│   │   └── Core/
│   │       ├── HomeController.php        # Ex-HomeController.php
│   │       ├── SearchController.php      # Ex-SearchController.php
│   │       ├── AboutController.php       # Se "QuemSouController" for sobre a página "Sobre"
│   │       ├── OfflineController.php     # Se existir e for algo "core"
│   │       └── QuemSouController.php     # Ou unificado em AboutController.php
│   ├── Models/
│   │   ├── User.php                      # Ex-UserModel.php (se for o caso)
│   │   ├── Community.php
│   │   ├── Forum.php
│   │   ├── Post.php
│   │   └── Comment.php
│   ├── Views/
│   │   ├── auth/
│   │   │   ├── login.php                 # Ex-login.php
│   │   │   └── register.php              # Ex-register.php
│   │   ├── community/
│   │   │   ├── index.php                 # Ex-comunidade/index.php
│   │   │   ├── view.php                  # Ex-comunidade/foruns.php ou algo similar
│   │   │   ├── create.php                # Se houver criação de algo na comunidade
│   │   │   └── forum/
│   │   │       ├── index.php             # Listagem de fóruns
│   │   │       └── thread.php            # Visualização de um tópico específico
│   │   ├── profile/
│   │   │   ├── view.php                  # Ex-perfil/home.php ou algo similar
│   │   │   ├── editar.php                # Ex-perfil/editar.php
│   │   │   ├── favoritos.php             # Ex-perfil/favoritos.php
│   │   │   ├── gerenciar.php             # Ex-perfil/gerenciar.php
│   │   │   ├── historico.php             # Ex-perfil/historico.php
│   │   │   ├── redefinir.php             # Ex-perfil/redefinir.php
│   │   │   ├── salvos.php                # Ex-perfil/salvos.php
│   │   │   ├── biblioteca.php            # Se for parte do perfil
│   │   │   └── book-detail.php           # Se for detalhe de livro no perfil
│   │   ├── home/
│   │   │   └── index.php                 # Ex-inicio ou ex-home.php
│   │   └── partials/
│   │       ├── header.php                # Ex-header.php
│   │       ├── footer.php                # Ex-footer.php
│   │       ├── sidebar.php               # Ex-sidebar.php
│   │       └── cards/
│   │           ├── community-card.php
│   │           └── user-card.php
│   ├── Routes/
│   │   ├── web.php
│   │   └── api.php                       # Adicionado, caso use rotas de API
│   ├── Services/
│   │   ├── AuthService.php
│   │   ├── CommunityService.php
│   │   └── SearchService.php
│   ├── Helpers/
│   │   ├── StringHelper.php
│   │   └── DateHelper.php
│   └── Middleware/
│       ├── AuthMiddleware.php
│       └── AdminMiddleware.php
├── storage/
│   ├── logs/
│   ├── cache/
│   └── uploads/
├── tests/
│   ├── Unit/
│   └── Integration/
├── vendor/
├── .env
├── .htaccess
├── composer.json
└── README.md
