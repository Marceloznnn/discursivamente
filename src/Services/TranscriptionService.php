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
        // 1) Verifica se a URL retorna 200 OK
        $headers = @get_headers($videoUrl, 1);        if (!$headers || strpos($headers[0], '200') === false) {
            throw new Exception("Vídeo não encontrado em: {$videoUrl}");
        }

        // 2) Baixa o MP4 para um arquivo temporário
        $tmpMp4 = sys_get_temp_dir() . DIRECTORY_SEPARATOR . uniqid('vid_') . '.mp4';
        
        $content = @file_get_contents($videoUrl);
        if ($content === false) {
            throw new Exception("Falha ao baixar vídeo de: {$videoUrl}");
        }
        $bytesSaved = file_put_contents($tmpMp4, $content);

        // 3) Executa o script Python que usa Whisper
        $script = $this->projectDir . '/transcribe.py';
        $cmd = sprintf('python "%s" "%s"', $script, $tmpMp4);
        
        exec($cmd, $output, $ret);

        // 4) Limpa o MP4 temporário
        if (unlink($tmpMp4)) {
        } else {
        }

        if ($ret !== 0 || empty($output[0])) {
            throw new Exception("Erro na transcrição (código {$ret})");
        }
        
        // 5) Move o .vtt gerado para public/legendas/
        $vttTmp  = trim($output[0]);
        $vttName = basename($vttTmp);
        $vttDest = $this->projectDir . '/public/legendas/' . $vttName;
        
        if (!is_dir(dirname($vttDest))) {
            if (mkdir(dirname($vttDest), 0777, true)) {
            } else {
                throw new Exception("Não foi possível criar o diretório de legendas");
            }
        }
        
        if (rename($vttTmp, $vttDest)) {
        } else {
            throw new Exception("Falha ao mover arquivo de legenda");
        }

        // 6) Retorna o caminho público
        $publicPath = '/legendas/' . $vttName;
        return $publicPath;
    }
}
