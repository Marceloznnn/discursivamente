/**
 * Sistema de upload avançado com suporte para arquivos grandes
 * Este script permite o upload de vídeos e imagens grandes com feedback
 * visual de progresso e validação do lado do cliente.
 */
class AdvancedUploader {
  constructor(options = {}) {    this.options = {
      maxImageSize: 50 * 1024 * 1024, // 50MB
      maxVideoSize: 200 * 1024 * 1024, // 200MB
      allowedImageTypes: [
        "image/jpeg",
        "image/png",
        "image/webp",
        "image/gif",
        "image/svg+xml",
      ],
      allowedVideoTypes: ["video/mp4", "video/webm", "video/ogg"],
      ...options,
    };

    this.uploading = false;
    this.progress = 0;

    // Bind methods
    this.handleFileSelect = this.handleFileSelect.bind(this);
    this.uploadFile = this.uploadFile.bind(this);
    this.validateFile = this.validateFile.bind(this);
    this.showPreview = this.showPreview.bind(this);
  }

  /**
   * Inicializa o uploader em um elemento de input
   * @param {string|HTMLElement} inputSelector - Seletor ou elemento do input
   * @param {object} options - Opções adicionais
   */
  init(inputSelector, options = {}) {
    this.options = { ...this.options, ...options };

    this.inputElement =
      typeof inputSelector === "string"
        ? document.querySelector(inputSelector)
        : inputSelector;

    if (!this.inputElement) {
      console.error("Elemento de input não encontrado:", inputSelector);
      return;
    }

    // Configurar o elemento de input
    this.inputElement.addEventListener("change", this.handleFileSelect);

    // Criar elementos para exibição de progresso e preview se não existirem
    const container = this.inputElement.parentElement;

    if (!container.querySelector(".upload-preview")) {
      const previewEl = document.createElement("div");
      previewEl.className = "upload-preview";
      previewEl.style.marginTop = "10px";
      previewEl.style.display = "none";
      container.appendChild(previewEl);
      this.previewElement = previewEl;
    } else {
      this.previewElement = container.querySelector(".upload-preview");
    }

    if (!container.querySelector(".upload-progress")) {
      const progressContainer = document.createElement("div");
      progressContainer.className = "upload-progress";
      progressContainer.style.marginTop = "10px";
      progressContainer.style.display = "none";

      const progressBar = document.createElement("div");
      progressBar.className = "progress-bar";
      progressBar.style.height = "5px";
      progressBar.style.backgroundColor = "#eee";
      progressBar.style.borderRadius = "3px";
      progressBar.style.overflow = "hidden";

      const progressFill = document.createElement("div");
      progressFill.className = "progress-fill";
      progressFill.style.height = "100%";
      progressFill.style.backgroundColor = "#007bff";
      progressFill.style.width = "0%";

      progressBar.appendChild(progressFill);
      progressContainer.appendChild(progressBar);

      const progressText = document.createElement("div");
      progressText.className = "progress-text";
      progressText.style.marginTop = "5px";
      progressText.style.fontSize = "0.8rem";
      progressText.style.color = "#666";
      progressText.textContent = "Preparando upload...";

      progressContainer.appendChild(progressText);
      container.appendChild(progressContainer);

      this.progressElement = progressContainer;
      this.progressFill = progressFill;
      this.progressText = progressText;
    } else {
      this.progressElement = container.querySelector(".upload-progress");
      this.progressFill = this.progressElement.querySelector(".progress-fill");
      this.progressText = this.progressElement.querySelector(".progress-text");
    }

    console.log("Advanced Uploader inicializado com sucesso!");
    console.log("Limites configurados:", {
      Imagens: this.formatBytes(this.options.maxImageSize),
      Vídeos: this.formatBytes(this.options.maxVideoSize),
    });
  }

  /**
   * Manipula a seleção de arquivos
   * @param {Event} event - Evento de mudança do input
   */
  handleFileSelect(event) {
    if (!event.target.files || !event.target.files.length) return;

    const file = event.target.files[0];
    const validationResult = this.validateFile(file);

    if (validationResult.valid) {
      this.showPreview(file);
      if (this.options.autoUpload) {
        this.uploadFile(file);
      }
    } else {
      this.showError(validationResult.message);
      this.resetInput();
    }
  }

  /**
   * Valida um arquivo
   * @param {File} file - O arquivo a ser validado
   * @returns {object} - Resultado da validação
   */
  validateFile(file) {
    const isImage = this.options.allowedImageTypes.includes(file.type);
    const isVideo = this.options.allowedVideoTypes.includes(file.type);

    if (!isImage && !isVideo) {
      return {
        valid: false,
        message: `Tipo de arquivo não suportado. Use apenas: ${this.options.allowedImageTypes
          .concat(this.options.allowedVideoTypes)
          .join(", ")}`,
      };
    }

    const maxSize = isImage
      ? this.options.maxImageSize
      : this.options.maxVideoSize;

    if (file.size > maxSize) {
      return {
        valid: false,
        message: `O arquivo é muito grande. O tamanho máximo é ${this.formatBytes(
          maxSize
        )}.`,
      };
    }

    return { valid: true };
  }

  /**
   * Exibe uma prévia do arquivo
   * @param {File} file - O arquivo para pré-visualização
   */
  showPreview(file) {
    if (!this.previewElement) return;

    this.previewElement.style.display = "block";
    this.previewElement.innerHTML = "";

    const isImage = this.options.allowedImageTypes.includes(file.type);
    const isVideo = this.options.allowedVideoTypes.includes(file.type);

    if (isImage) {
      const img = document.createElement("img");
      img.style.maxWidth = "100%";
      img.style.maxHeight = "200px";
      img.style.borderRadius = "4px";
      img.style.objectFit = "contain";

      const reader = new FileReader();
      reader.onload = (e) => {
        img.src = e.target.result;
      };
      reader.readAsDataURL(file);

      this.previewElement.appendChild(img);
    } else if (isVideo) {
      const video = document.createElement("video");
      video.controls = true;
      video.muted = true;
      video.style.maxWidth = "100%";
      video.style.maxHeight = "200px";
      video.style.borderRadius = "4px";

      const source = document.createElement("source");
      source.src = URL.createObjectURL(file);
      source.type = file.type;

      video.appendChild(source);
      this.previewElement.appendChild(video);

      // Limpar o objeto URL quando não for mais necessário
      video.addEventListener("loadeddata", () => {
        video
          .play()
          .then(() => {
            setTimeout(() => {
              video.pause();
              video.currentTime = 0;
            }, 3000); // Reproduz por 3 segundos e pausa
          })
          .catch((e) =>
            console.log("Não foi possível reproduzir o vídeo automaticamente")
          );
    }

    // Adicionar informações do arquivo
    const fileInfo = document.createElement("div");
    fileInfo.style.marginTop = "5px";
    fileInfo.style.fontSize = "0.8rem";
    fileInfo.innerHTML = `
            <strong>${file.name}</strong><br>
            Tipo: ${file.type}<br>
            Tamanho: ${this.formatBytes(file.size)}
        `;
    this.previewElement.appendChild(fileInfo);
  }

  /**
   * Faz o upload do arquivo para o servidor
   * @param {File} file - O arquivo a ser enviado
   */
  uploadFile(file) {
    if (this.uploading) {
      console.warn("Já existe um upload em andamento.");
      return;
    }

    this.uploading = true;
    this.updateProgress(0, "Iniciando upload...");
    this.progressElement.style.display = "block";

    const formData = new FormData();
    formData.append(this.options.fileFieldName || "file", file);

    if (this.options.extraData) {
      Object.entries(this.options.extraData).forEach(([key, value]) => {
        formData.append(key, value);
      });
    }

    const xhr = new XMLHttpRequest();

    xhr.upload.addEventListener("progress", (event) => {
      if (event.lengthComputable) {
        const progress = Math.round((event.loaded / event.total) * 100);
        this.updateProgress(
          progress,
          `${this.formatBytes(event.loaded)} de ${this.formatBytes(
            event.total
          )} (${progress}%)`
        );
      }
    });

    xhr.addEventListener("load", () => {
      if (xhr.status >= 200 && xhr.status < 300) {
        this.updateProgress(100, "Upload concluído com sucesso!");
        setTimeout(() => {
          this.progressElement.style.display = "none";
        }, 2000);

        if (this.options.onSuccess) {
          try {
            const response = JSON.parse(xhr.responseText);
            this.options.onSuccess(response);
          } catch (e) {
            this.options.onSuccess(xhr.responseText);
          }
        }
      } else {
        this.updateProgress(0, `Erro: ${xhr.status} ${xhr.statusText}`);
        if (this.options.onError) {
          this.options.onError(xhr);
        }
      }
      this.uploading = false;
    });

    xhr.addEventListener("error", () => {
      this.updateProgress(0, "Erro de conexão");
      if (this.options.onError) {
        this.options.onError(xhr);
      }
      this.uploading = false;
    });

    xhr.addEventListener("abort", () => {
      this.updateProgress(0, "Upload cancelado");
      if (this.options.onAbort) {
        this.options.onAbort();
      }
      this.uploading = false;
    });

    xhr.open("POST", this.options.uploadUrl || "/upload");
    xhr.send(formData);

    return xhr; // Retorna o objeto XHR para possível cancelamento
  }

  /**
   * Upload de arquivo grande em chunks
   * @param {File} file - O arquivo a ser enviado
   * @param {number} chunkSize - Tamanho de cada chunk em bytes
   */
  uploadFileWithChunks(file, chunkSize = 2 * 1024 * 1024) {
    if (this.uploading) {
      console.warn("Já existe um upload em andamento.");
      return;
    }

    // Se o arquivo não for grande o suficiente, use o método padrão
    if (file.size < 10 * 1024 * 1024) {
      console.log("Arquivo não é grande o suficiente para upload em chunks, usando método padrão");
      return this.uploadFile(file);
    }

    this.uploading = true;
    this.updateProgress(0, "Preparando upload em partes...");
    this.progressElement.style.display = "block";

    const totalChunks = Math.ceil(file.size / chunkSize);
    console.log(`Arquivo será enviado em ${totalChunks} partes de ${this.formatBytes(chunkSize)}`);

    // Criar array para armazenar IDs dos chunks
    const chunkIds = [];
    let currentChunk = 0;
    
    // Função para upload de um chunk
    const uploadNextChunk = () => {
      if (currentChunk >= totalChunks) {
        // Todos os chunks foram enviados, finalize o upload
        this.finalizeChunkedUpload(chunkIds, file);
        return;
      }
      
      // Calcular o início e fim do chunk atual
      const start = currentChunk * chunkSize;
      const end = Math.min(start + chunkSize, file.size);
      const chunk = file.slice(start, end);
      
      // Criar FormData para o chunk
      const formData = new FormData();
      formData.append("chunk", chunk);
      formData.append("chunkIndex", currentChunk);
      formData.append("totalChunks", totalChunks);
      formData.append("fileName", file.name);
      formData.append("fileSize", file.size);
      formData.append("fileType", file.type);
      
      if (this.options.extraData) {
        Object.entries(this.options.extraData).forEach(([key, value]) => {
          formData.append(key, value);
        });
      }
      
      // URL para o endpoint de upload de chunks
      const url = this.options.chunkUploadUrl || (this.options.uploadUrl + "/chunk");
      
      // Enviar chunk
      const xhr = new XMLHttpRequest();
      
      xhr.open("POST", url);
      
      xhr.onload = () => {
        if (xhr.status >= 200 && xhr.status < 300) {
          try {
            const response = JSON.parse(xhr.responseText);
            chunkIds.push(response.chunkId);
            
            // Atualizar progresso
            const progress = Math.round(((currentChunk + 1) / totalChunks) * 100);
            this.updateProgress(
              progress, 
              `Parte ${currentChunk + 1} de ${totalChunks} (${progress}%)`
            );
            
            // Ir para próximo chunk
            currentChunk++;
            uploadNextChunk();
          } catch (e) {
            this.showError("Erro ao processar resposta do servidor: " + e.message);
            this.uploading = false;
          }
        } else {
          this.showError(`Erro ao enviar parte ${currentChunk + 1}: ${xhr.status} ${xhr.statusText}`);
          this.uploading = false;
        }
      };
      
      xhr.onerror = () => {
        this.showError(`Falha de conexão ao enviar parte ${currentChunk + 1}`);
        this.uploading = false;
      };
      
      xhr.upload.onprogress = (e) => {
        if (e.lengthComputable) {
          const chunkProgress = Math.round((e.loaded / e.total) * 100);
          const overallProgress = Math.round(
            ((currentChunk * chunkSize + e.loaded) / file.size) * 100
          );
          
          this.updateProgress(
            overallProgress,
            `Parte ${currentChunk + 1}/${totalChunks}: ${chunkProgress}% (Total: ${overallProgress}%)`
          );
        }
      };
      
      xhr.send(formData);
    };
    
    // Iniciar o upload
    uploadNextChunk();
  }
  
  /**
   * Finaliza um upload em chunks
   * @param {Array} chunkIds - Array com os IDs dos chunks
   * @param {File} file - Arquivo original
   */
  finalizeChunkedUpload(chunkIds, file) {
    this.updateProgress(100, "Finalizando upload...");
    
    const formData = new FormData();
    formData.append("chunkIds", JSON.stringify(chunkIds));
    formData.append("fileName", file.name);
    formData.append("fileType", file.type);
    formData.append("fileSize", file.size);
    
    if (this.options.extraData) {
      Object.entries(this.options.extraData).forEach(([key, value]) => {
        formData.append(key, value);
      });
    }
    
    // URL para finalizar o upload
    const url = this.options.finalizeUrl || (this.options.uploadUrl + "/finalize");
    
    const xhr = new XMLHttpRequest();
    xhr.open("POST", url);
    
    xhr.onload = () => {
      if (xhr.status >= 200 && xhr.status < 300) {
        try {
          const response = JSON.parse(xhr.responseText);
          this.updateProgress(100, "Upload concluído com sucesso!");
          
          setTimeout(() => {
            this.progressElement.style.display = "none";
          }, 2000);
          
          if (this.options.onSuccess) {
            this.options.onSuccess(response);
          }
        } catch (e) {
          this.showError("Erro ao finalizar upload: " + e.message);
        }
      } else {
        this.showError(`Erro ao finalizar upload: ${xhr.status} ${xhr.statusText}`);
      }
      
      this.uploading = false;
    };
    
    xhr.onerror = () => {
      this.showError("Falha de conexão ao finalizar upload");
      this.uploading = false;
    };
    
    xhr.send(formData);
  }

  /**
   * Ativa o log no console para debug
   * @param {boolean} enabled - Se o log deve estar ativado
   */
  enableDebugLogs(enabled = true) {
    this.debugEnabled = enabled;
    
    if (enabled) {
      this.debug("Debug logs ativados");
      this.debug("Configurações:", this.options);
    }
  }
  
  /**
   * Registra mensagens de debug no console
   */
  debug(...args) {
    if (this.debugEnabled) {
      console.log("[AdvancedUploader]", ...args);
    }
  }

  /**
   * Atualiza a barra de progresso
   * @param {number} percent - Porcentagem de conclusão
   * @param {string} message - Mensagem de status
   */
  updateProgress(percent, message) {
    if (!this.progressFill || !this.progressText) return;

    this.progress = percent;
    this.progressFill.style.width = `${percent}%`;
    this.progressText.textContent = message;
  }

  /**
   * Exibe uma mensagem de erro
   * @param {string} message - Mensagem de erro
   */
  showError(message) {
    console.error("Erro de upload:", message);

    if (this.options.onError) {
      this.options.onError({ message });
    }

    // Se tem uma função de notificação definida, use-a
    if (window.showNotification) {
      window.showNotification(message, "error");
    } else {
      // Caso contrário, crie uma notificação simples
      const notification = document.createElement("div");
      notification.className = "upload-error-notification";
      notification.style.padding = "10px";
      notification.style.backgroundColor = "#f44336";
      notification.style.color = "white";
      notification.style.borderRadius = "4px";
      notification.style.marginTop = "10px";
      notification.style.position = "fixed";
      notification.style.bottom = "20px";
      notification.style.right = "20px";
      notification.style.zIndex = "1000";
      notification.textContent = message;

      document.body.appendChild(notification);

      setTimeout(() => {
        notification.style.opacity = "0";
        notification.style.transition = "opacity 0.5s";
        setTimeout(() => {
          document.body.removeChild(notification);
        }, 500);
      }, 5000);
    }
  }

  /**
   * Limpa o input de arquivo
   */
  resetInput() {
    if (!this.inputElement) return;
    this.inputElement.value = "";
  }

  /**
   * Formata bytes para uma representação legível
   * @param {number} bytes - Número de bytes
   * @param {number} decimals - Casas decimais
   * @returns {string} - Representação formatada
   */
  formatBytes(bytes, decimals = 2) {
    if (bytes === 0) return "0 Bytes";

    const k = 1024;
    const dm = decimals < 0 ? 0 : decimals;
    const sizes = ["Bytes", "KB", "MB", "GB", "TB"];

    const i = Math.floor(Math.log(bytes) / Math.log(k));

    return parseFloat((bytes / Math.pow(k, i)).toFixed(dm)) + " " + sizes[i];
  }
}

// Expor o uploader globalmente
window.AdvancedUploader = AdvancedUploader;
