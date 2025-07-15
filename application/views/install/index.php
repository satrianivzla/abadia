<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($title) ? html_escape($title) : 'Instalador'; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f0f2f5;
        }
        .installer-container {
            max-width: 600px;
            margin-top: 5rem;
        }
    </style>
</head>
<body>
    <div class="container installer-container">
        <div class="card shadow-sm">
            <div class="card-header text-center">
                <h3>Instalador de la Aplicación de Agentes</h3>
            </div>
            <div class="card-body p-4">
                <?php if (isset($error)): ?>
                    <div class="alert alert-danger">
                        <h4>Error de Instalación</h4>
                        <p><?php echo $error; ?></p>
                    </div>
                <?php elseif (isset($success)): ?>
                    <div class="alert alert-success">
                        <h4>¡Instalación Exitosa!</h4>
                        <p><?php echo $success; ?></p>
                    </div>
                <?php else: ?>
                    <p>Bienvenido al instalador. Este proceso configurará la base de datos necesaria para que la aplicación funcione.</p>
                    <p><strong>Antes de continuar, por favor asegúrese de que:</strong></p>
                    <ul>
                        <li>Ha configurado sus credenciales de base de datos en <code>application/config/database.php</code>.</li>
                        <li>La base de datos especificada existe y está vacía.</li>
                    </ul>
                    <p>Haga clic en el siguiente botón para iniciar la instalación.</p>
                    <hr>
                    <div class="d-grid">
                        <?php echo form_open(site_url('install')); ?>
                            <input type="hidden" name="install" value="1">
                            <button type="submit" class="btn btn-primary btn-lg">Instalar Base de Datos Ahora</button>
                        <?php echo form_close(); ?>
                    </div>
                <?php endif; ?>
            </div>
            <div class="card-footer text-center text-muted">
                &copy; <?php echo date('Y'); ?>
            </div>
        </div>
    </div>
</body>
</html>
