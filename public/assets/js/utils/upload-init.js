/**
 * Script de inicialização para o sistema avançado de upload
 * Este script conecta o AdvancedUploader com os formulários existentes no sistema
 */

document.addEventListener("DOMContentLoaded", function () {
  // Verificar se o AdvancedUploader está disponível
  if (typeof AdvancedUploader === "undefined") {
    console.error(
      "AdvancedUploader não está carregado. Verifique se o arquivo advanced-uploader.js está incluído."
    );
    return;
  }

  // Configurar para uploads de vídeo
  const videoUploads = document.querySelectorAll(
    'input[type="file"][accept*="video"]'
  );
  if (videoUploads.length > 0) {
    videoUploads.forEach((input) => {
      const uploader = new AdvancedUploader({
        maxVideoSize: 200 * 1024 * 1024, // 200MB para vídeos
        allowedVideoTypes: ["video/mp4", "video/webm", "video/ogg"],
        onSuccess: function (response) {
          console.log("Vídeo enviado com sucesso:", response);
          // Se existir um campo oculto para armazenar a URL, atualize-o
          const urlField = input
            .closest("form")
            .querySelector('input[type="hidden"][name*="url"]');
          if (urlField && response.url) {
            urlField.value = response.url;
          }

          // Atualizar qualquer visualização existente
          const previewVideo = document.querySelector("#material-video");
          if (previewVideo && response.url) {
            previewVideo.src = response.url;
          }
        },
        onError: function (error) {
          console.error("Erro no upload do vídeo:", error);
          if (window.showNotification) {
            window.showNotification(
              "Erro no upload do vídeo: " +
                (error.message || "Verifique o console para mais detalhes"),
              "error"
            );
          }
        },
      });

      uploader.init(input, {
        uploadUrl: input.form ? input.form.action : "/upload",
        fileFieldName: input.name || "file",
        autoUpload: true,
      });

      console.log("Uploader de vídeo inicializado para", input);
    });
  }

  // Configurar para uploads de imagem
  const imageUploads = document.querySelectorAll(
    'input[type="file"][accept*="image"]'
  );
  if (imageUploads.length > 0) {
    imageUploads.forEach((input) => {
      const uploader = new AdvancedUploader({
        maxImageSize: 50 * 1024 * 1024, // 50MB para imagens
        allowedImageTypes: [
          "image/jpeg",
          "image/png",
          "image/webp",
          "image/gif",
          "image/svg+xml",
        ],
        onSuccess: function (response) {
          console.log("Imagem enviada com sucesso:", response);
          // Se existir um campo oculto para armazenar a URL, atualize-o
          const urlField = input
            .closest("form")
            .querySelector(
              'input[type="hidden"][name*="url"], input[type="hidden"][name*="image"]'
            );
          if (urlField && response.url) {
            urlField.value = response.url;
          }

          // Se for um upload de avatar, atualizar a imagem de perfil
          if (input.id === "avatar-upload" && response.url) {
            const profileImg = document.querySelector(".profile-avatar img");
            if (profileImg) {
              profileImg.src = response.url;
            }
          }
        },
        onError: function (error) {
          console.error("Erro no upload da imagem:", error);
          if (window.showNotification) {
            window.showNotification(
              "Erro no upload da imagem: " +
                (error.message || "Verifique o console para mais detalhes"),
              "error"
            );
          }
        },
      });

      uploader.init(input, {
        uploadUrl: input.form ? input.form.action : "/upload",
        fileFieldName: input.name || "file",
        autoUpload: input.hasAttribute("data-auto-upload") !== false,
      });

      console.log("Uploader de imagem inicializado para", input);
    });
  }

  console.log("Sistema avançado de upload inicializado com sucesso!");
});
