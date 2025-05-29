# Script para iniciar os servidores PHP e WebSocket (Fórum e Suporte)
$ErrorActionPreference = 'Stop'

function Start-Servers {
    $phpServer = $null
    $wsForumServer = $null
    $wsSupportServer = $null

    try {
        # Inicia o servidor PHP
        Write-Host "Iniciando servidor PHP na porta 8000..." -ForegroundColor Green
        $phpServer = Start-Process powershell -ArgumentList "-NoProfile -Command php -S localhost:8000 -t public" -PassThru

        # Inicia o WebSocket do Fórum
        Write-Host "Iniciando WebSocket (Fórum) na porta 8080..." -ForegroundColor Green
        $wsForumServer = Start-Process powershell -ArgumentList "-NoProfile -Command php websocket-server.php" -PassThru

        # Inicia o WebSocket de Suporte
        Write-Host "Iniciando WebSocket (Suporte) na porta 8081..." -ForegroundColor Green
        $wsSupportServer = Start-Process powershell -ArgumentList "-NoProfile -Command php websocket-support-server.php" -PassThru

        Write-Host "`nTodos os servidores foram iniciados com sucesso!" -ForegroundColor Green
        Write-Host "- Site:           http://localhost:8000" -ForegroundColor Yellow
        Write-Host "- WS Fórum:       ws://localhost:8080" -ForegroundColor Yellow
        Write-Host "- WS Suporte:     ws://localhost:8081" -ForegroundColor Yellow
        Write-Host "`nPressione Ctrl+C para parar todos os servidores" -ForegroundColor Gray

        # Aguarda até que algum servidor caia ou o usuário pare manualmente
        while ($true) {
            Start-Sleep -Seconds 1
            if ($phpServer.HasExited -or $wsForumServer.HasExited -or $wsSupportServer.HasExited) {
                throw "Um dos servidores parou inesperadamente"
            }
        }
    }
    catch {
        Write-Host "`nParando todos os servidores..." -ForegroundColor Yellow
    }
    finally {
        if ($phpServer -and -not $phpServer.HasExited) {
            Stop-Process -Id $phpServer.Id -Force
        }
        if ($wsForumServer -and -not $wsForumServer.HasExited) {
            Stop-Process -Id $wsForumServer.Id -Force
        }
        if ($wsSupportServer -and -not $wsSupportServer.HasExited) {
            Stop-Process -Id $wsSupportServer.Id -Force
        }

        Write-Host "Todos os servidores foram finalizados." -ForegroundColor Green
    }
}

# Define o diretório atual como raiz do projeto
Set-Location -Path $PSScriptRoot

# Instala dependências se necessário
if (Test-Path "composer.json") {
    Write-Host "Verificando dependências do Composer..." -ForegroundColor Blue
    composer install --no-interaction
}

# Inicia os servidores
Start-Servers
 