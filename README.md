# 📚 Sistema de Gerenciamento de Curso

Este é um sistema desenvolvido em PHP com suporte a rotas personalizadas, controle de acesso e renderização via Twig. Ele permite o gerenciamento de cursos, usuários, turmas, conversas, mensagens, eventos e mais.

## 🚀 Funcionalidades

- Autenticação de usuários (aluno, professor, administrador)
- Gerenciamento de membros do curso
- Criação e visualização de turmas
- Sistema de mensagens e conversas
- Integração com eventos e feedbacks
- Renderização com Twig
- Middleware de controle de acesso

## 📁 Estrutura de Pastas

```
├── core/               # Lógica principal (roteador, middlewares, controllers base)
├── controllers/        # Controladores da aplicação
├── models/             # Modelos de dados
├── views/              # Templates Twig
├── public/             # Ponto de entrada para o servidor (index.php)
├── routes/             # Definições de rotas
└── vendor/             # Dependências (composer)
```

## ▶️ Como iniciar o servidor local

1. Certifique-se de ter o PHP instalado (versão 7.4+):
   ```bash
   php -v
   ```

2. Navegue até o diretório do projeto:
   ```bash
   cd /c/xampp/htdocs/jf
   ```

3. Inicie o servidor embutido do PHP apontando para a pasta `public`:
   ```bash
   php -S localhost:8000 -t public
   ```

4. Acesse no navegador:
   ```
   http://localhost:8000
   ```

## 🛠 Requisitos

- PHP 7.4 ou superior
- Composer
- XAMPP (opcional, se usar Apache + MySQL)

## 📦 Instalação de dependências

Execute o seguinte comando para instalar as dependências do projeto:

```bash
composer install
```

## 📄 Licença

Este projeto está licenciado sob a [MIT License](LICENSE).