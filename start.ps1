# Script para iniciar os servidores PHP e WebSocket
$ErrorActionPreference = 'Stop'

function Start-Servers {
    $phpServer = $null
    $wsServer = $null
    
    try {
        # Inicia o servidor PHP
        Write-Host "Iniciando servidor PHP na porta 8000..." -ForegroundColor Green
        $phpServer = Start-Process powershell -ArgumentList "-NoProfile -Command php -S localhost:8000 -t public" -PassThru
        
        # Inicia o servidor WebSocket
        Write-Host "Iniciando servidor WebSocket na porta 8080..." -ForegroundColor Green
        $wsServer = Start-Process powershell -ArgumentList "-NoProfile -Command php websocket-server.php" -PassThru
        
        Write-Host "`nServidores iniciados com sucesso!" -ForegroundColor Green
        Write-Host "- Acesse o site em: http://localhost:8000" -ForegroundColor Yellow
        Write-Host "- WebSocket rodando em: ws://localhost:8080" -ForegroundColor Yellow
        Write-Host "`nPressione Ctrl+C para parar os servidores" -ForegroundColor Gray
        
        # Aguarda até que o usuário pressione Ctrl+C
        while ($true) {
            Start-Sleep -Seconds 1
            
            # Verifica se algum dos servidores parou inesperadamente
            if ($phpServer.HasExited -or $wsServer.HasExited) {
                throw "Um dos servidores parou inesperadamente"
            }
        }
    } 
    catch {
        Write-Host "`nParando servidores..." -ForegroundColor Yellow
    }
    finally {
        # Garante que ambos os servidores sejam parados
        if ($phpServer -and -not $phpServer.HasExited) {
            Stop-Process -Id $phpServer.Id -Force
        }
        if ($wsServer -and -not $wsServer.HasExited) {
            Stop-Process -Id $wsServer.Id -Force
        }
        
        Write-Host "Servidores parados." -ForegroundColor Green
    }
}

# Muda para o diretório do script
Set-Location -Path $PSScriptRoot

# Verifica se o Composer está atualizado
if (Test-Path "composer.json") {
    Write-Host "Verificando dependências do Composer..." -ForegroundColor Blue
    composer install --no-interaction
}

# Inicia os servidores
Start-Servers
