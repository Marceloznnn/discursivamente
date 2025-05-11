# Discursivamente 2.1

Plataforma de aprendizado e discussão acadêmica.

## Requisitos

- PHP 8.0 ou superior
- MySQL 5.7 ou superior
- Composer
- Node.js 14 ou superior
- XAMPP (para ambiente de desenvolvimento)

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

# Discursivamente2.1 — Guia de Scripts

Este documento lista e explica os scripts disponíveis no projeto **Discursivamente2.1**, tanto do **npm** (front‑end) quanto do **Composer** (back‑end).

---

## 1. Configuração Inicial

Antes de qualquer coisa, instale as dependências do projeto:

```bash
# Front‑end
npm install

# Back‑end
composer install
```

---

## 2. Scripts de Desenvolvimento (Front‑end)

Use o prefixo `npm run` seguido do nome do script.

| Script             | Comando                 | O que faz                                                                           |
| ------------------ | ----------------------- | ----------------------------------------------------------------------------------- |
| **clean**          | `npm run clean`         | Remove caches, logs e build antigo: `var/cache/* var/log/* public/assets/build/*`   |
| **lint\:js**       | `npm run lint:js`       | Executa ESLint em `public/assets/js/**/*.js` e corrige problemas simples            |
| **lint\:css**      | `npm run lint:css`      | Executa Stylelint em `public/assets/css/**/*.css` e corrige problemas simples       |
| **lint**           | `npm run lint`          | Combina `lint:js` + `lint:css`                                                      |
| **test\:js**       | `npm run test:js`       | Roda os testes JS com Jest                                                          |
| **test**           | `npm test`              | Alias para `npm run test:js`                                                        |
| **build\:js**      | `npm run build:js`      | Minifica e concatena JS em `public/assets/build/app.min.js`                         |
| **build\:css**     | `npm run build:css`     | Minifica CSS em `public/assets/build/app.min.css`                                   |
| **build**          | `npm run build`         | Limpa + Lint + Build de JS e CSS                                                    |
| **watch\:js**      | `npm run watch:js`      | Observa alterações em JS e recompila automaticamente                                |
| **watch\:css**     | `npm run watch:css`     | Observa alterações em CSS e recompila automaticamente                               |
| **watch**          | `npm run watch`         | Roda `watch:js` e `watch:css` simultaneamente (via concurrently)                    |
| **dev**            | `npm run dev`           | Alias para `npm run watch`                                                          |
| **deploy\:assets** | `npm run deploy:assets` | Instala dependências e gera assets (`npm ci && npm run build`)                      |
| **deploy**         | `npm run deploy`        | Deploy completo front‑end + comandos SSH no servidor (build, pull, composer, cache) |

Exemplo de uso em dev:

```bash
npm run dev
```

---

## 3. Scripts de Desenvolvimento (Back‑end)

Use `composer run-script <nome>` ou apenas `composer <nome>` quando for script direto.

| Script                        | Comando                            | O que faz                                                               |
| ----------------------------- | ---------------------------------- | ----------------------------------------------------------------------- |
| **test**                      | `composer test`                    | Executa PHPUnit (`phpunit --colors`)                                    |
| **clean**                     | `composer run-script clean`        | Remove arquivos em `var/cache/*.php` e `var/log/*.log`                  |
| **build-assets**              | `composer run-script build-assets` | Chama `npm ci` + `npm run build`                                        |
| **post-root-package-install** | (automático)                       | Copia `.env.example` para `.env`, se não existir                        |
| **post-update-cmd**           | (automático)                       | Publica assets de vendor (`artisan vendor:publish`)                     |
| **serve**                     | `composer serve`                   | Inicia servidor PHP embutido em `localhost:8000`                        |
| **deploy**                    | `composer run-script deploy`       | Pipeline: install otimizado, build-assets, migrations e cache para prod |

Exemplo de uso em ambiente local:

```bash
composer serve
```

---

## 4. Fluxos Comuns

### Desenvolvimento Completo

```bash
# Front‑end e back‑end
npm install && composer install
npm run dev        # front‑end em watch
composer serve     # back‑end em localhost:8000
```

### Testes

```bash
# Testes JS
npm test

# Testes PHP
composer test
```

### Build para Produção

```bash
npm run build       # gera assets otimizados
composer run-script build-assets
composer run-script clean
```

### Deploy (Servidor)

```bash
# No servidor:
git pull origin main
npm run deploy      # front‑end + SSH + composer + cache
composer run-script deploy   # back‑end completo
```

---
