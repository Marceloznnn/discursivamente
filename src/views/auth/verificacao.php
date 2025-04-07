<?php
// Definição de constante de segurança para evitar acesso direto
if (!defined('BASE_PATH')) {
    define('BASE_PATH', dirname(__DIR__, 2));
}

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['temp_register_data'])) {
    header('Location: /register');
    exit;
}

$pageTitle = "Verificação de Conta - DISCURSIVAMENTE";

require_once BASE_PATH . '/src/views/partials/header.php';

$contactType = $_SESSION['temp_register_data']['contact_type'] ?? 'email';
$contactValue = $contactType === 'email' 
    ? ($_SESSION['temp_register_data']['email'] ?? '')
    : ($_SESSION['temp_register_data']['telefone'] ?? '');

if ($contactType === 'email') {
    $parts = explode('@', $contactValue);
    if (count($parts) == 2) {
        $username = $parts[0];
        $domain = $parts[1];
        $maskedUsername = substr($username, 0, 3) . str_repeat('*', max(strlen($username) - 3, 1));
        $maskedContact = $maskedUsername . '@' . $domain;
    } else {
        $maskedContact = $contactValue;
    }
} else {
    $digits = preg_replace('/\D/', '', $contactValue);
    $maskedContact = '(' . substr($digits, 0, 2) . ') *****-' . substr($digits, -4);
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="Verifique seu código de acesso para criar sua conta no DISCURSIVAMENTE">
  <title><?php echo htmlspecialchars($pageTitle); ?></title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    .form-control:focus {
      border-color: #0d6efd;
      box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
    }
    .card {
      border-radius: 1rem;
      box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    }
    .card-header {
      border-radius: 1rem 1rem 0 0 !important;
    }
    .btn-primary {
      padding: 0.6rem;
    }
    .verification-code {
      display: flex;
      gap: 0.5rem;
      justify-content: center;
      margin: 2rem 0;
    }
    .verification-code input {
      width: 3rem;
      height: 3rem;
      text-align: center;
      font-size: 1.5rem;
      border-radius: 0.5rem;
      border: 1px solid #ced4da;
    }
    .verification-code input:focus {
      border-color: #0d6efd;
      box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
      outline: none;
    }
    .timer {
      font-size: 0.9rem;
      color: #6c757d;
      margin-bottom: 1rem;
    }
  </style>
</head>
<body class="bg-light">
  <div class="container py-5">
    <div class="row justify-content-center">
      <div class="col-md-6">
        <div class="card">
          <div class="card-header bg-primary text-white text-center py-3">
            <h3 class="mb-0">Verifique sua conta</h3>
            <p class="mb-0 small">Digite o código de verificação enviado</p>
          </div>
          <div class="card-body p-4">
            <?php if (isset($_SESSION['mensagem'])): ?>
              <div class="alert alert-<?php echo $_SESSION['tipo_mensagem'] ?? 'info'; ?> alert-dismissible fade show">
                <?php 
                  echo htmlspecialchars($_SESSION['mensagem']); 
                  unset($_SESSION['mensagem'], $_SESSION['tipo_mensagem']);
                ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
              </div>
            <?php endif; ?>
            <div class="text-center mb-4">
              <p>Enviamos um código de verificação para <?php echo htmlspecialchars($contactType === 'email' ? 'o email' : 'o telefone'); ?>:</p>
              <p class="fw-bold"><?php echo htmlspecialchars($maskedContact); ?></p>
            </div>
            <form id="verificationForm" action="/verify-code" method="POST" class="needs-validation" novalidate>
              <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
              <div class="verification-code">
                <input type="text" name="code[]" maxlength="1" pattern="\d" class="form-control" required>
                <input type="text" name="code[]" maxlength="1" pattern="\d" class="form-control" required>
                <input type="text" name="code[]" maxlength="1" pattern="\d" class="form-control" required>
                <input type="text" name="code[]" maxlength="1" pattern="\d" class="form-control" required>
                <input type="text" name="code[]" maxlength="1" pattern="\d" class="form-control" required>
                <input type="text" name="code[]" maxlength="1" pattern="\d" class="form-control" required>
              </div>
              <div class="text-center timer">
                <p>Código válido por <span id="countdown">5:00</span></p>
              </div>
              <div class="d-grid gap-2">
                <button type="submit" class="btn btn-primary btn-lg">
                  <i class="fas fa-check-circle me-2"></i>Verificar
                </button>
              </div>
            </form>
            <div class="text-center mt-4">
              <p>Não recebeu o código? <a href="/resend-code" class="text-primary">Reenviar código</a></p>
              <p><a href="/register" class="text-secondary"><i class="fas fa-arrow-left me-1"></i>Voltar ao registro</a></p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const form = document.getElementById('verificationForm');
      const codeInputs = document.querySelectorAll('input[name="code[]"]');
      codeInputs.forEach(function(input, index) {
        input.addEventListener('input', function() {
          if (this.value.length === 1 && index < codeInputs.length - 1) {
            codeInputs[index + 1].focus();
          }
        });
        input.addEventListener('keydown', function(event) {
          if (event.key === 'Backspace' && !this.value && index > 0) {
            codeInputs[index - 1].focus();
          }
        });
      });
      form.addEventListener('submit', function(event) {
        let isValid = true;
        codeInputs.forEach(input => {
          if (!input.value || !/^\d$/.test(input.value)) {
            input.classList.add('is-invalid');
            isValid = false;
          } else {
            input.classList.remove('is-invalid');
          }
        });
        if (!isValid) {
          event.preventDefault();
          event.stopPropagation();
        }
      });
      let timeLeft = 5 * 60;
      const countdownEl = document.getElementById('countdown');
      const timer = setInterval(function() {
        const minutes = Math.floor(timeLeft / 60);
        const seconds = timeLeft % 60;
        countdownEl.textContent = `${minutes}:${seconds.toString().padStart(2, '0')}`;
        if (timeLeft <= 0) {
          clearInterval(timer);
          countdownEl.textContent = 'Expirado';
        }
        timeLeft--;
      }, 1000);
    });
  </script>
</body>
</html>
