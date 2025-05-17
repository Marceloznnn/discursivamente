@echo off
REM Ajuste o caminho abaixo se o seu php.exe estiver em outra pasta
SET PHP_EXE=C:\xampp\php\php.exe
REM Porta e pasta pública
SET HOST=0.0.0.0
SET PORT=8000
SET DOC_ROOT=%~dp0public

echo Iniciando servidor PHP em http://%HOST%:%PORT% a partir de %DOC_ROOT% ...
"%PHP_EXE%" -S %HOST%:%PORT% -t "%DOC_ROOT%"
pause
