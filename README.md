# Documentação Interna

Este diretório contém documentos internos que não devem ser acessados publicamente.
Acesso restrito a membros autorizados do projeto.


1. Visão Geral do Projeto
Nome do Projeto: discursivamente
Domínio: discursivamente.com.br
Objetivo: Disponibilizar uma interface amigável e organizada onde:

Usuários podem visualizar livros (Biblioteca)

Professores podem postar atividades

Usuários interagem nos fóruns

Há páginas institucionais (Quem Somos)

Usuários gerenciam seus perfis

Sistema de autenticação via página de Login

Banco de Dados:

Nome: discusivamente_db

Usuário: root

Senha: root

2. Arquitetura e Organização do Projeto
2.1. Estrutura de Pastas e Arquivos
Uma organização modular e orientada a pastas facilita a manutenção, escalabilidade e clareza do projeto. Segue abaixo uma sugestão de estrutura com caminhos absolutos:

DISCURSIVAMENTE/
├── cache/
│   └── views/
│       ├── .htaccess
│       └── clear_cache.php
├── logs/
│   ├── access.log
│   ├── config.php
│   ├── error.log
│   └── rotate_logs.php
├── private/
│   ├── docs/
│   │   ├── .htaccess
│   │   ├── README.md
│   │   └── SEGURANCA.md
│   └── keys/
│       ├── .htaccess
│       └── README.txt
├── public/
│   ├── css/
│   ├── images/
│   ├── js/
│   ├── favicon.ico
│   └── index.php
│
├───├── src/
│   │   ├── routes/
│   │   └── views/
│   │       ├── comunicacao/
│   │       │   ├── clube_leitura.php
│   │       │   └── forum.php
│   │       ├── partials/
│   │       │   ├── footer.php
│   │       │   ├── header_perso.php
│   │       │   └── header.php
│   │       └── perfil/
│   │           ├── biblioteca.php
│   │           ├── book-detail.php
│   │           ├── comunicacao.php
│   │           ├── home.php
│   │           ├── login.php
│   │           ├── logout.php
│   │           ├── perfil.php
│   │           ├── quem-somos.php
│   │           └── register.php
│   └── storage/
│       ├── backups/
│       │   ├── .htaccess
│       │   └── backup_db.php
│       ├── uploads/
│       │   ├── .htaccess
│       │   └── config.php
│       └── tmp/
│           └── session/
│               ├── .htaccess
│               ├── check_login.php
│               └── cleanup.php
└── vendor/
    ├── .htaccess
    └── README.md




2.2. Explicação dos Principais Componentes
Public
/public/index.php: Ponto de entrada do sistema. Usando URLs amigáveis e caminhos absolutos, redireciona as requisições para o sistema de rotas.

/public/css, /public/js, /public/images: Pasta para recursos estáticos. O uso de caminhos absolutos facilita a referência desses arquivos em todo o projeto.

Src
/src/config/database.php: Arquivo responsável pela conexão com o banco de dados, utilizando os dados fornecidos.
Exemplo de código de conexão (PHP PDO):

php
Copiar
Editar
<?php
// Caminho absoluto: /discursivamente/src/config/database.php
define('DB_HOST', 'localhost');
define('DB_NAME', 'discusivamente_db');
define('DB_USER', 'root');
define('DB_PASS', 'root');

try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erro na conexão com o banco de dados: " . $e->getMessage());
}
?>
/src/controllers: Contém a lógica de cada página. Cada controlador será responsável por processar dados, interagir com os modelos e encaminhar informações para as views.

/src/models: Aqui ficam as classes que representam as entidades do sistema (usuário, livro, atividade, etc.) e a lógica de acesso ao banco de dados.

/src/views: Arquivos de template que serão renderizados para cada página. Cada arquivo corresponde a uma página principal (home, fóruns, biblioteca, etc.).

/src/routes/web.php: Define as rotas amigáveis do sistema, ligando as URLs aos respectivos controladores.
Exemplo simples de definição de rota:

php
Copiar
Editar
<?php
// Caminho absoluto: /discursivamente/src/routes/web.php

// Exemplo usando um framework próprio ou micro-framework
$router->get('/', 'HomeController@index');
$router->get('/foruns', 'ForumController@index');
$router->get('/biblioteca', 'BibliotecaController@index');
$router->get('/quem-somos', 'QuemSomosController@index');
$router->get('/perfil', 'PerfilController@index');
$router->get('/login', 'LoginController@index');
?>
Vendor
Gerenciamento de dependências utilizando Composer (caso seja necessário).

.htaccess
Arquivo de configuração para reescrita de URLs, garantindo que todas as requisições sejam direcionadas para o ponto de entrada do sistema e fortalecendo a segurança.

3. Caminhos Absolutos
Para garantir a utilização de caminhos absolutos no projeto, recomenda-se:

Definir uma constante de base (ex.: BASE_PATH) logo no arquivo de entrada (index.php) e utilizá-la para incluir outros arquivos.

Exemplo:

php
Copiar
Editar
<?php
// Caminho absoluto definido a partir da raiz do projeto
define('BASE_PATH', __DIR__);

// Incluindo a configuração do banco de dados
require_once BASE_PATH . '/src/config/database.php';
?>
4. Integração com o Banco de Dados
A integração será feita via PDO (no exemplo acima) ou utilizando uma camada de ORM se o projeto crescer. A ideia é centralizar a conexão em um único arquivo (/src/config/database.php), facilitando:

Manutenção da conexão

Tratamento de exceções e erros

Reutilização da conexão em todos os controladores e modelos

5. Considerações Finais
Modularidade: Cada funcionalidade (home, fóruns, biblioteca, quem somos, perfil, login) possui seu próprio controlador, view e, se necessário, modelos dedicados.

Escalabilidade: A estrutura permite a fácil inclusão de novas páginas ou funcionalidades, bastando criar novos arquivos nas pastas correspondentes.

Segurança e Produção: A presença do arquivo .htaccess, uso de caminhos absolutos e centralização da conexão com o banco de dados são práticas que facilitam a migração para um ambiente de produção.

Documentação: Recomenda-se manter um arquivo README.md com informações sobre instalação, configuração, dependências e instruções de deploy.

Essa estrutura inicial serve como base para a implementação página a página. Conforme o projeto evoluir, a modularização pode ser aprofundada e outros padrões de design (como MVC completo, injeção de dependências, etc.) podem ser incorporados para garantir robustez e manutenibilidade.
#   d i s c u r s i v a m e n t e  
 