# Instruções para Configuração de Limites de Upload no PHP

Os testes indicaram que as configurações de limite de upload através de `.user.ini` e `.htaccess` não estão sendo completamente aplicadas pelo PHP. Para garantir que os limites sejam configurados adequadamente, siga estas instruções:

## 1. Configuração via php.ini Principal

A maneira mais confiável de definir estes limites é através do arquivo `php.ini` principal:

1. **Localize seu arquivo php.ini principal**:

   - Em instalações XAMPP: `C:\xampp\php\php.ini`
   - No Linux: normalmente em `/etc/php/VERSION/apache2/php.ini` ou `/etc/php.ini`

2. **Edite o arquivo php.ini e procure pelas seguintes diretivas**:

   ```ini
   ; Maximum allowed size for uploaded files.
   upload_max_filesize = 200M

   ; Must be greater than or equal to upload_max_filesize
   post_max_size = 200M

   ; Maximum execution time of each script, in seconds
   max_execution_time = 600

   ; Maximum amount of time each script may spend parsing request data
   max_input_time = 600

   ; Maximum amount of memory a script may consume
   memory_limit = 512M
   ```

3. **Reinicie o servidor Apache após as alterações**:
   - No XAMPP: use o Painel de Controle do XAMPP para parar e iniciar o Apache
   - No Linux: `sudo service apache2 restart`

## 2. Verificação da Configuração

Após fazer as alterações e reiniciar o servidor, verifique se os novos limites foram aplicados:

1. Acesse a página `phpinfo.php` para confirmar que os valores foram atualizados
2. Utilize a página `upload_diagnostics.php` para verificar o status de todas as configurações

## 3. Testes de Upload

A página de teste de upload (`upload_test.php`) foi criada para ajudar a verificar se os limites estão sendo respeitados. Use-a para:

1. Testar o upload de arquivos de tamanhos variados
2. Conferir se o sistema registra corretamente os erros
3. Garantir que o upload em partes (chunks) está funcionando para arquivos grandes

## 4. Se Ainda Houver Problemas

Se após seguir as instruções acima ainda houver problemas com os limites de upload:

1. **Verifique se há arquivos .user.ini em outras pastas** que podem estar sobrescrevendo suas configurações
2. **Verifique seu servidor web**: algumas configurações podem precisar ser ajustadas no Apache ou Nginx
3. **Consulte os logs**: verifique os logs de erro do Apache e do PHP para identificar mensagens relacionadas

## Acompanhamento de Logs

O sistema de log aprimorado registra todas as tentativas de upload em:

- `logs/uploads.log`: registros de uploads bem-sucedidos
- `logs/errors.log`: registros de erros de upload

Use a ferramenta de visualização de log (`viewlog.php`) para analisar os registros de upload e diagnosticar problemas.
