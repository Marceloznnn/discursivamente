<?php
// filepath: c:/xampp/htdocs/Discursivamente2.1/src/Services/TranscriptionService.php

namespace Services;

use Exception;

class TranscriptionService
{
    private string $projectDir;

    public function __construct(string $projectDir)
    {
        $this->projectDir = rtrim($projectDir, '/\\');
    }

    /**
     * Gera um arquivo .vtt a partir da URL pública do vídeo.
     *
     * @param string $videoUrl URL completa do vídeo no Cloudinary (secure_url)
     * @return string Caminho público relativo ao .vtt (ex: '/legendas/xxx.vtt')
     * @throws Exception em caso de falha no download ou na transcrição
     */    public function generateSubtitleFromUrl(string $videoUrl): string
    {
        error_log("[Legenda] Iniciando geração para vídeo: {$videoUrl}");

        // 1) Verifica se a URL retorna 200 OK
        error_log("[Legenda] Verificando disponibilidade do vídeo...");
        $headers = @get_headers($videoUrl, 1);        if (!$headers || strpos($headers[0], '200') === false) {
            error_log("[Legenda] ERRO: Vídeo não encontrado (HTTP " . (isset($headers[0]) ? $headers[0] : 'sem resposta') . ")");
            throw new Exception("Vídeo não encontrado em: {$videoUrl}");
        }
        error_log("[Legenda] Vídeo encontrado e disponível");

        // 2) Baixa o MP4 para um arquivo temporário
        $tmpMp4 = sys_get_temp_dir() . DIRECTORY_SEPARATOR . uniqid('vid_') . '.mp4';
        error_log("[Legenda] Baixando vídeo para {$tmpMp4}...");
        
        $content = @file_get_contents($videoUrl);
        if ($content === false) {
            error_log("[Legenda] ERRO: Falha ao baixar vídeo");
            throw new Exception("Falha ao baixar vídeo de: {$videoUrl}");
        }
        $bytesSaved = file_put_contents($tmpMp4, $content);
        error_log("[Legenda] Vídeo baixado com sucesso: {$bytesSaved} bytes");

        // 3) Executa o script Python que usa Whisper
        $script = $this->projectDir . '/transcribe.py';
        $cmd = sprintf('python "%s" "%s"', $script, $tmpMp4);
        error_log("[Legenda] Executando transcrição: {$cmd}");
        
        exec($cmd, $output, $ret);
        error_log("[Legenda] Código de retorno da transcrição: {$ret}");
        error_log("[Legenda] Saída da transcrição: " . implode("\n", $output));

        // 4) Limpa o MP4 temporário
        if (unlink($tmpMp4)) {
            error_log("[Legenda] Arquivo temporário removido: {$tmpMp4}");
        } else {
            error_log("[Legenda] AVISO: Não foi possível remover arquivo temporário: {$tmpMp4}");
        }

        if ($ret !== 0 || empty($output[0])) {
            error_log("[Legenda] ERRO: Falha na transcrição");
            throw new Exception("Erro na transcrição (código {$ret})");
        }
        error_log("[Legenda] Transcrição concluída com sucesso");        // 5) Move o .vtt gerado para public/legendas/
        $vttTmp  = trim($output[0]);
        $vttName = basename($vttTmp);
        $vttDest = $this->projectDir . '/public/legendas/' . $vttName;
        
        error_log("[Legenda] Movendo arquivo de legenda:");
        error_log("[Legenda] - Origem: {$vttTmp}");
        error_log("[Legenda] - Destino: {$vttDest}");
        
        if (!is_dir(dirname($vttDest))) {
            error_log("[Legenda] Criando diretório de legendas...");
            if (mkdir(dirname($vttDest), 0777, true)) {
                error_log("[Legenda] Diretório criado com sucesso");
            } else {
                error_log("[Legenda] ERRO: Falha ao criar diretório");
                throw new Exception("Não foi possível criar o diretório de legendas");
            }
        }
        
        if (rename($vttTmp, $vttDest)) {
            error_log("[Legenda] Arquivo movido com sucesso");
        } else {
            error_log("[Legenda] ERRO: Falha ao mover arquivo");
            throw new Exception("Falha ao mover arquivo de legenda");
        }

        // 6) Retorna o caminho público
        $publicPath = '/legendas/' . $vttName;
        error_log("[Legenda] Processo concluído. Caminho público: {$publicPath}");
        return $publicPath;
    }
}
