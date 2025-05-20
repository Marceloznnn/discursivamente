# Scripts de Manutenção e Backup — Discursivamente 2.1

Este diretório contém scripts PHP para manutenção, backup e otimização do sistema Discursivamente 2.1. Eles são utilitários importantes para garantir a segurança, integridade e performance do projeto.

## Pré-requisitos
- PHP 7.4+
- Extensões: `pdo_mysql`, `zip` (para backup de arquivos)
- Composer (para autoload e Dotenv)
- Banco de dados MySQL
- Variáveis de ambiente configuradas no arquivo `.env` na raiz do projeto

## Scripts Disponíveis

### 1. `scripts/backup.php`
**Função:**
- Realiza backup completo do banco de dados e dos arquivos essenciais do projeto.
- Permite também restaurar o banco a partir de um backup `.sql`.
- Mantém apenas os 5 backups mais recentes para evitar acúmulo.

**Como executar:**

- Para criar backup:
  ```sh
  php scripts/backup.php backup
  ```
  Isso irá gerar dois arquivos em `/backups`: um `.sql` (banco) e um `.zip` (arquivos).

- Para restaurar backup do banco:
  ```sh
  php scripts/backup.php restore
  ```
  O script listará os backups disponíveis e pedirá confirmação antes de restaurar.

**O que faz:**
- Usa `mysqldump` se disponível, ou faz dump via PDO.
- Compacta diretórios importantes (uploads, src, assets, .env) em um `.zip`.
- Limpa backups antigos automaticamente.

### 2. `scripts/db_optimizer.php`
**Função:**
- (Opcional) Otimiza tabelas do banco de dados, removendo fragmentação e melhorando performance.
- Pode ser customizado para rodar comandos como `OPTIMIZE TABLE` em todas as tabelas.

**Como executar:**
```sh
php scripts/db_optimizer.php
```

### 3. `scripts/performance_monitor.php`
**Função:**
- (Opcional) Monitora o desempenho do sistema, registrando métricas de uso, tempo de resposta e gargalos.
- Gera logs em `/logs/performance/`.

**Como executar:**
```sh
php scripts/performance_monitor.php
```

### 4. `scripts/security_optimization.php`
**Função:**
- (Opcional) Executa verificações de segurança e aplica otimizações recomendadas.
- Pode checar permissões, arquivos sensíveis, e sugerir melhorias.

**Como executar:**
```sh
php scripts/security_optimization.php
```

### 5. `scripts/maintenance/maintenance.php`
**Função:**
- (Opcional) Centraliza tarefas de manutenção agendada, como limpeza de logs, atualização de caches, etc.

**Como executar:**
```sh
php scripts/maintenance/maintenance.php
```

---

## Observações
- Todos os scripts devem ser executados a partir da raiz do projeto ou do diretório `scripts/`.
- Certifique-se de que as variáveis de ambiente do banco estejam corretas no `.env`.
- O backup de arquivos pode demorar dependendo do volume de uploads e assets.
- Recomenda-se agendar o backup automático via cron ou agendador do Windows.

---

Dúvidas ou problemas? Consulte a documentação interna ou entre em contato com o responsável técnico do projeto.
