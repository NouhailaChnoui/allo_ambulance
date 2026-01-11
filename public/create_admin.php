<?php
// public/create_admin.php
// URL: http://127.0.0.1:8000/create_admin.php

require dirname(__DIR__).'/vendor/autoload.php';

use App\Entity\User;
use Symfony\Component\Dotenv\Dotenv;

// 1. Charger les variables d'environnement D'ABORD
$dotenv = new Dotenv();

// Essayer .env.local d'abord, puis .env
$envFile = dirname(__DIR__).'/.env.local';
if (!file_exists($envFile)) {
    $envFile = dirname(__DIR__).'/.env';
}
$dotenv->load($envFile);

// 2. Définir les variables si elles n'existent pas
if (!isset($_SERVER['APP_ENV'])) {
    $_SERVER['APP_ENV'] = $_ENV['APP_ENV'] ?? 'dev';
}
if (!isset($_SERVER['APP_DEBUG'])) {
    $_SERVER['APP_DEBUG'] = $_ENV['APP_DEBUG'] ?? '1';
}

// 3. Créer le kernel
$kernel = new App\Kernel($_SERVER['APP_ENV'], (bool) $_SERVER['APP_DEBUG']);
$kernel->boot();

// 4. Services
$container = $kernel->getContainer();
$em = $container->get('doctrine.orm.entity_manager');
$hasher = $container->get('security.user_password_hasher');

// 5. Logique de création admin
$repo = $em->getRepository(User::class);
$admin = $repo->findOneBy(['email' => 'admin@alloambulance.com']);

if (!$admin) {
    $admin = new User();
    $admin->setEmail('admin@alloambulance.com');
    $admin->setNom('Admin');
    $admin->setPrenom('System');
    $admin->setTelephone('0600000000');
    $admin->setAdresse('Siège Administratif');
    $message = "créé";
} else {
    $message = "mis à jour";
}

// Hasher le mot de passe
$admin->setPassword($hasher->hashPassword($admin, 'admin123'));
$admin->setRoles(['ROLE_ADMIN']);

// Sauvegarder
$em->persist($admin);
$em->flush();

// 6. Réponse HTML
header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin Created - Allo Ambulance</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .card {
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            border: none;
        }
        .card-header {
            border-radius: 15px 15px 0 0 !important;
            background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
        }
        .btn-success {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            border: none;
        }
        .btn-primary {
            background: linear-gradient(135deg, #007bff 0%, #6610f2 100%);
            border: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="card">
                    <div class="card-header text-white text-center py-4">
                        <h1 class="mb-0"><i class="fas fa-user-shield"></i> Admin <?php echo strtoupper($message); ?></h1>
                        <p class="mb-0">Allo Ambulance System</p>
                    </div>
                    <div class="card-body p-4">
                        <div class="alert alert-success text-center">
                            <h4><i class="fas fa-check-circle"></i> Opération réussie!</h4>
                            <p class="mb-0">L'administrateur a été <?php echo $message; ?> avec succès.</p>
                        </div>
                        
                        <div class="card border-info mb-4">
                            <div class="card-header bg-info text-white">
                                <h5 class="mb-0"><i class="fas fa-key"></i> Identifiants de connexion</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label"><i class="fas fa-envelope"></i> <strong>Email</strong></label>
                                            <div class="input-group">
                                                <input type="text" class="form-control" value="admin@alloambulance.com" id="emailInput" readonly>
                                                <button class="btn btn-outline-secondary" onclick="copyToClipboard('emailInput')">
                                                    <i class="fas fa-copy"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label"><i class="fas fa-lock"></i> <strong>Mot de passe</strong></label>
                                            <div class="input-group">
                                                <input type="text" class="form-control" value="admin123" id="passwordInput" readonly>
                                                <button class="btn btn-outline-secondary" onclick="copyToClipboard('passwordInput')">
                                                    <i class="fas fa-copy"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="alert alert-warning">
                            <h5><i class="fas fa-exclamation-triangle"></i> Important !</h5>
                            <p class="mb-0">Notez bien ces identifiants. Pour des raisons de sécurité, modifiez le mot de passe après votre première connexion.</p>
                        </div>
                        
                        <div class="text-center mt-4">
                            <h5>Étapes suivantes :</h5>
                            <ol class="list-group list-group-numbered mb-4">
                                <li class="list-group-item">Copiez les identifiants ci-dessus</li>
                                <li class="list-group-item">Cliquez sur le bouton "Connexion Admin"</li>
                                <li class="list-group-item">Collez les identifiants dans le formulaire</li>
                                <li class="list-group-item">Accédez au tableau de bord admin</li>
                            </ol>
                            
                            <div class="d-grid gap-3">
                                <a href="/admin/login" class="btn btn-primary btn-lg">
                                    <i class="fas fa-sign-in-alt"></i> Connexion Admin
                                </a>
                                <a href="/" class="btn btn-secondary">
                                    <i class="fas fa-home"></i> Retour à l'accueil
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer text-center text-muted">
                        <small>
                            <i class="fas fa-shield-alt"></i> 
                            Cette page est sécurisée. Supprimez ce fichier (create_admin.php) après utilisation.
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function copyToClipboard(elementId) {
            var copyText = document.getElementById(elementId);
            copyText.select();
            copyText.setSelectionRange(0, 99999); // Pour mobile
            navigator.clipboard.writeText(copyText.value);
            
            // Notification visuelle
            var btn = copyText.nextElementSibling;
            var originalHTML = btn.innerHTML;
            btn.innerHTML = '<i class="fas fa-check"></i>';
            btn.classList.remove('btn-outline-secondary');
            btn.classList.add('btn-success');
            
            setTimeout(function() {
                btn.innerHTML = originalHTML;
                btn.classList.remove('btn-success');
                btn.classList.add('btn-outline-secondary');
            }, 2000);
        }
        
        // Auto-copy au chargement de la page
        document.addEventListener('DOMContentLoaded', function() {
            // Optionnel: auto-copier l'email
            // copyToClipboard('emailInput');
        });
    </script>
</body>
</html>