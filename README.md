# Discursivamente 2.1

Plataforma de aprendizado e discussão acadêmica.

## Requisitos

- PHP 8.0 ou superior
- MySQL 5.7 ou superior
- Composer
- Node.js 14 ou superior
- XAMPP (para ambiente de desenvolvimento)

## Instalação

1. Clone o repositório:
```bash
git clone https://github.com/marcelo/Discursivamente2.1.git
cd Discursivamente2.1
```

2. Instale as dependências do PHP:
```bash
composer install
```

3. Instale as dependências do Node.js:
```bash
npm install
```

4. Copie o arquivo de ambiente:
```bash
cp .env.example .env
```

5. Configure o arquivo `.env` com suas credenciais:
```env
APP_ENV=development
APP_DEBUG=true

DB_HOST=127.0.0.1
DB_PORT=3306
DB_NAME=discursivamente_db
DB_USER=root
DB_PASS=

MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=projeto.discursivamente@gmail.com
MAIL_PASSWORD=frue xhal ioqg oqvq
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=projeto.discursivamente@gmail.com
MAIL_FROM_NAME=Discursivamente
```

6. Crie o banco de dados:
```bash
mysql -u root -p
CREATE DATABASE discursivamente_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

7. Execute as migrações:
```bash
php src/Database/migrate.php
```

8. Configure as tarefas agendadas (como administrador):
```bash
php src/Scripts/install_cron.php
```

## Estrutura do Projeto

```
Discursivamente2.1/
├── .env                   # Configurações de ambiente
├── .gitignore             # Arquivos ignorados pelo Git
├── README.md              # Este arquivo
├── composer.json          # Dependências PHP
├── composer.lock          # Versões exatas das dependências PHP
├── package.json           # Dependências Node.js
├── package-lock.json      # Versões exatas das dependências Node.js
├── phpunit.xml            # Configuração do PHPUnit
├── robots.txt             # Configuração para crawlers
├── database/              # Migrações e backups
│   └── backups/           # Backups do banco de dados
│       ├── backup_discursivamente_db_2025-04-21_060002.sql
│       ├── backup_discursivamente_db_2025-04-22_083404.sql
│       ├── backup_discursivamente_db_2025-04-26_060002.sql
│       ├── backup_discursivamente_db_2025-04-27_065039.sql
│       └── backup_discursivamente_db_2025-04-27_090001.sql
├── node_modules/          # Dependências Node.js
├── public/                # Arquivos públicos
│   ├── .htaccess          # Configurações do Apache
│   ├── assets/            # Assets compilados (CSS, JS, imagens)
│   │   ├── build/
│   │   ├── css/
│   │   ├── images/
│   │   └── js/
│   ├── favicon.ico
│   ├── favicon-16x16.png
│   ├── favicon-32x32.png
│   ├── android-chrome-192x192.png
│   ├── android-chrome-512x512.png
│   ├── apple-touch-icon.png
│   ├── index.php          # Ponto de entrada da aplicação
│   ├── manifest.json      # Manifest para PWA
│   ├── service-worker.js  # Service Worker para PWA
│   ├── site.webmanifest
│   └── uploads/           # Uploads de usuários
│       └── avatars/
├── src/                   # Código fonte
│   ├── App/               # Lógica da aplicação
│   │   └── Models/        # Modelos de dados
│   ├── Cache/             # Sistema de cache
│   │   └── FileCache.php
│   ├── Config/            # Configurações
│   │   ├── Config.php
│   │   ├── cron.php
│   │   ├── database.php
│   │   └── routes.php
│   ├── Controllers/       # Controladores
│   │   ├── Controller.php
│   │   └── Web/
│   ├── Core/              # Classes principais
│   │   ├── Database/
│   │   ├── Twig/
│   │   └── View.php
│   ├── Database/          # Classes de banco de dados
│   │   ├── Connection.php
│   │   ├── Migrations/
│   │   └── admin_setup.sql
│   ├── Infrastructure/    # Infraestrutura
│   │   ├── Database/
│   │   ├── Mail/
│   │   ├── Persistence/
│   ├── Middleware/        # Middlewares
│   │   ├── AdminMiddleware.php
│   │   ├── AuthMiddleware.php
│   │   └── SecurityHeadersMiddleware.php
│   ├── Routes/            # Rotas
│   │   ├── Router.php
│   │   └── web.php
│   ├── Scripts/           # Scripts de manutenção
│   │   ├── backup_database.php
│   │   ├── clean_cache.php
│   │   ├── create_admin.php
│   │   ├── install_cron.php
│   │   ├── maintenance.php
│   │   └── rotate_logs.php
│   ├── Security/          # Classes de segurança
│   │   ├── Auth.php
│   │   ├── CacheManager.php
│   │   ├── Csrf.php
│   │   ├── Logger.php
│   │   └── SecurityMiddleware.php
│   ├── Services/          # Serviços
│   │   ├── AuthService.php
│   │   ├── BaseService.php
│   │   ├── CloudinaryService.php
│   │   └── SecurityLogService.php
│   ├── Utils/             # Utilitários
│   │   └── Logger.php
│   └── Views/             # Views
│       ├── admin/
│       ├── auth/
│       ├── biblioteca/
│       ├── compromissos/
│       ├── comunidade/
│       ├── errors/
│       ├── home/
│       ├── layouts/
│       ├── legal/
│       ├── perfil/
│       └── quem-somos/
├── var/                   # Arquivos variáveis
│   ├── cache/             # Cache
│   │   └── twig/
│   ├── log/               # Logs
│   │   ├── cache-cleaner.log
│   │   ├── cache_clean.log
│   │   ├── cache_clean.log.gz
│   │   ├── database-backup.log
│   │   ├── log-rotator.log
│   │   └── maintenance.log
│   └── sessions/          # Sessões
├── vendor/                # Dependências PHP
│   ├── autoload.php
│   ├── bin/
│   ├── cloudinary/
│   ├── composer/
│   ├── doctrine/
│   ├── graham-campbell/
│   ├── guzzlehttp/
│   ├── intervention/
│   ├── matthiasmullie/
│   ├── monolog/
│   ├── myclabs/
│   ├── nikic/
│   ├── phar-io/
│   ├── phpmailer/
│   ├── phpoption/
│   ├── phpunit/
│   ├── predis/
│   ├── psr/
│   ├── ralouphie/
│   ├── sebastian/
│   ├── symfony/
│   ├── theseer/
│   ├── twig/
│   └── vlucas/

## Scripts de Manutenção

O sistema inclui vários scripts de manutenção que são executados automaticamente:

1. **Limpeza de Cache** (`clean_cache.php`)
   - Remove arquivos de cache antigos
   - Configurável em `maintenance.cache_cleanup`
   - Executa diariamente às 02:00

2. **Rotação de Logs** (`rotate_logs.php`)
   - Comprime e remove logs antigos
   - Configurável em `maintenance.log_rotation`
   - Executa diariamente às 03:00

3. **Manutenção do Sistema** (`maintenance.php`)
   - Verifica espaço em disco
   - Otimiza banco de dados
   - Verifica permissões
   - Limpa arquivos temporários
   - Limpa sessões antigas
   - Executa semanalmente aos domingos às 04:00

4. **Backup do Banco** (`backup_database.php`)
   - Faz backup do banco de dados
   - Comprime os backups
   - Mantém histórico configurável
   - Executa diariamente às 01:00

## Desenvolvimento

1. Inicie o servidor de desenvolvimento:
```bash
php -S localhost:8000 -t public/
```

2. Compile os assets:
```bash
npm run dev
```

3. Para produção:
```bash
npm run build
```

## Testes

Execute os testes com PHPUnit:
```bash
composer test
```

## Segurança

- Todas as senhas são hasheadas com bcrypt
- Proteção contra CSRF em todos os formulários
- Sanitização de entrada de dados
- Proteção contra XSS
- Rate limiting nas APIs
- Sessões seguras com lifetime configurável
- Logs de atividades suspeitas

## Contribuindo

1. Faça um fork do projeto
2. Crie uma branch para sua feature (`git checkout -b feature/AmazingFeature`)
3. Commit suas mudanças (`git commit -m 'Add some AmazingFeature'`)
4. Push para a branch (`git push origin feature/AmazingFeature`)
5. Abra um Pull Request

## Licença

Este projeto está licenciado sob a licença MIT - veja o arquivo [LICENSE](LICENSE) para detalhes.

## Suporte

Para suporte, envie um email para projeto.discursivamente@gmail.com

## indentidade visual

 Estilo Visual e Estrutura
- A plataforma deve segue um estilo moderno, limpo e focado em experiência do usuário. - Utiliza bastante espaçamento, seções bem separadas e elementos visuais como banners, cards, ícones e botões grandes. - Tipografia clara, com títulos em negrito e textos de fácil leitura. - Layout responsivo, adaptando-se bem a diferentes tamanhos de tela (media queries presentes).

Seções em destaque:
Banner principal (hero) com chamada para ação.
Seção de pilares (Leitura, Comunidade, Metas) com ícones.
Depoimentos, eventos, perguntas frequentes, etc.
2. Cores Utilizadas (paleta principal)
Definidas em :root no CSS:

Primária: #5F0F40 (roxo escuro/vinho) – usada em botões, títulos e destaques.
Secundária: #615279 (roxo acinzentado) – usada para complementar elementos e fundos.
Texto: #333333 (cinza escuro) – cor principal dos textos.
Fundo claro: #f9f9f9 – para áreas de destaque, cartões e seções.
Branco: #ffffff – para fundos de cards, caixas e áreas internas.
Cinza médio: #707070 – para textos secundários e detalhes.
Cinza claro: #e5e5e5 – para bordas, divisores e fundos suaves.
Outros elementos:

Sombreamento sutil (rgba(0, 0, 0, 0.08)) para dar profundidade a cards e seções.
Transições suaves nos elementos interativos (all 0.3s ease).
3. Identidade Visual
- A identidade visual transmite modernidade, acolhimento e profissionalismo, com foco em leitura, comunidade e evolução. - Ícones de livro, usuários, metas e conquistas reforçam o tema educacional e de comunidade. - O uso de roxo/vinho como cor principal sugere criatividade, inspiração e um toque sofisticado, diferenciando-se de plataformas mais “frias”. - Botões e CTAs (call-to-action) são destacados com a cor primária, incentivando a navegação e engajamento.

4. Tipografia
Fonte principal: 'Segoe UI', Arial, sans-serif – moderna, legível e amigável.
Títulos em negrito, tamanhos grandes para seções principais.
Textos com espaçamento confortável, facilitando a leitura.
5. Outros Elementos Visuais
Cards com sombra e bordas arredondadas.
Ícones em SVG ou FontAwesome para reforçar a comunicação visual.
Animações suaves (ex: .fade-in, .animated-title) para dar vida à interface.
Imagens de fundo e ilustrações para criar um ambiente acolhedor e inspirador.
6. Resumo Profissional
Sua home utiliza uma identidade visual moderna, acolhedora e inspiradora, com foco em tons de roxo/vinho, branco e cinza, reforçando a proposta de comunidade e evolução na leitura. O design é responsivo, limpo e utiliza elementos visuais que facilitam o engajamento e a navegação.