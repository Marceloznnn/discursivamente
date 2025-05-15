# 🧠 Discursivamente

## 📝 Descrição

Discursivamente é uma aplicação web desenvolvida em PHP com o objetivo de [inserir aqui a descrição principal do projeto — ex: auxiliar alunos em cursos livres com conteúdo dinâmico].

## 🚀 Funcionalidades

- [Funcionalidade principal 1]
- [Funcionalidade principal 2]
- [Funcionalidade principal 3]
- [Funcionalidade principal 4]

## 🔧 Tecnologias Utilizadas

- PHP 7.4+
- MySQL/MariaDB
- HTML5
- CSS3
- JavaScript
- Composer
- [Outras bibliotecas/frameworks, se aplicável]

---

## ⚙️ Requisitos

- PHP 7.4 ou superior
- MySQL 5.7 ou superior
- Composer
- Servidor web (Apache, Nginx ou built-in do PHP)

---

## 📦 Instalação

1. Clone o repositório:
   ```bash
   git clone https://github.com/seu-usuario/discursivamente.git
   cd discursivamente
   ```

2. Instale as dependências do projeto:
   ```bash
   composer install
   ```

3. Configure o arquivo `.env` (se existir) ou as credenciais diretamente em um arquivo de configuração, como `config/database.php`.

4. Crie o banco de dados no MySQL:
   ```sql
   CREATE DATABASE discursivamente CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   ```

5. Execute os scripts de criação de tabelas ou migrações (caso existam).

---

## ▶️ Como iniciar o servidor

Se estiver usando o **servidor embutido do PHP**, inicie o servidor apontando para a pasta `public`:

```bash
php -S localhost:8000 -t public
```

> Isso garante que o roteamento funcione corretamente com o `index.php` localizado em `public/`.

---

## 🌍 Acessando via internet com Ngrok

Para expor seu projeto local para acesso externo:

1. Baixe e instale o Ngrok: https://ngrok.com/download
2. Execute o seguinte comando (com a mesma porta usada no PHP):

```bash
ngrok http 8000
```

3. O terminal mostrará um endereço público, como:
   ```
   https://a1b2c3d4.ngrok.io
   ```

Use esse endereço para testar seu site externamente ou integrar com serviços externos (como webhooks).

---

## 🧪 Ambiente de Desenvolvimento

- Utilize um ambiente como **XAMPP, Laragon ou Docker** se preferir um ambiente mais robusto.
- Utilize o arquivo `.env` para armazenar configurações sensíveis.
- Certifique-se de ativar **mod_rewrite** (caso use Apache) para permitir URLs amigáveis.

---

## 🛠️ Estrutura de Pastas (sugestão)

```
discursivamente/
├── app/               # Lógica da aplicação
├── public/            # Document root (ponto de entrada via navegador)
│   └── index.php
├── config/            # Arquivos de configuração
├── database/          # Scripts SQL ou migrações
├── resources/         # Views e assets
├── routes/            # Rotas do sistema
├── vendor/            # Dependências do Composer
└── .env               # Configurações do ambiente
```

---

## 📄 Licença

[Defina aqui o tipo de licença do projeto, como MIT, GPL, etc.]

---

## 🤝 Contribuições

Sinta-se à vontade para abrir issues ou pull requests. Toda ajuda é bem-vinda!
