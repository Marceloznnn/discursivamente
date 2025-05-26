<div class="admin-tools-panel">
    <h2>Ferramentas de Diagnóstico de Upload</h2>
    <p>Estas ferramentas ajudam a diagnosticar e corrigir problemas com uploads de arquivos grandes.</p>
    
    <div class="tool-links">
        <a href="upload_diagnostics.php" class="btn btn-primary">Diagnóstico do Sistema</a>
        <a href="upload_test.php" class="btn btn-info">Teste de Upload</a>
        <a href="viewlog.php?log=uploads" class="btn btn-secondary">Logs de Upload</a>
        <a href="viewlog.php?log=errors" class="btn btn-danger">Logs de Erro</a>
        <a href="phpinfo.php" class="btn btn-dark">Configuração PHP</a>
    </div>
    
    <div class="alert alert-info mt-3">
        <i class="fas fa-info-circle"></i> Estas ferramentas são visíveis apenas para administradores.
    </div>
</div>

<style>
.admin-tools-panel {
    background-color: #f8f9fa;
    border-radius: 8px;
    padding: 20px;
    margin-bottom: 20px;
    border-left: 4px solid #007bff;
}

.tool-links {
    margin: 15px 0;
}

.tool-links .btn {
    margin-right: 10px;
    margin-bottom: 10px;
}
</style>
