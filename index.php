<?php
// index.php
session_start();
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Login - SIPC</title>
  <link rel="stylesheet" href="css/style.css">
  <script src="js/script.js" defer></script>
  <style>
    .login-container {
      width: 320px;
      margin: 100px auto;
      background: #fff;
      padding: 20px;
      border-radius: 4px;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }
    .login-container h2 {
      text-align: center;
      margin-bottom: 20px;
      color: #004466;
    }
    .login-container .form-group {
      margin-bottom: 15px;
      display: flex;
      flex-direction: column;
    }
    .login-container .form-group label {
      margin-bottom: 5px;
      font-weight: bold;
    }
    .login-container .form-group input {
      padding: 8px;
      font-size: 14px;
      border: 1px solid #ccc;
      border-radius: 4px;
    }
    .login-container button {
      width: 100%;
      padding: 10px;
      background: #004466;
      color: #fff;
      border: none;
      border-radius: 4px;
      font-size: 16px;
      cursor: pointer;
    }
    .login-container .error {
      color: red;
      text-align: center;
      margin-bottom: 15px;
    }
  </style>
</head>
<body>
  <div class="login-container">
    <h2>Ingreso al SIPC</h2>
    <?php if (isset($_GET['error'])): ?>
      <p class="error"><?php echo htmlspecialchars($_GET['error']); ?></p>
    <?php endif; ?>
    <form action="process_login.php" method="post" id="loginForm">
      <div class="form-group">
        <label for="rut">RUT:</label>
        <input type="text" id="rut" name="rut" required placeholder="12.345.678-5">
      </div>
      <div class="form-group">
        <label for="password">Contraseña:</label>
        <input type="password" id="password" name="password" required>
      </div>
      <button type="submit">Ingresar</button>
    </form>
  </div>
</body>
</html>
