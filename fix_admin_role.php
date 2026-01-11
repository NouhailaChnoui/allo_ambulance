<?php
// fix_admin_role.php
require __DIR__.'/vendor/autoload.php';

use App\Kernel;
use App\Entity\User;

$kernel = new Kernel('dev', true);
$kernel->boot();

$container = $kernel->getContainer();
$em = $container->get('doctrine.orm.entity_manager');
$hasher = $container->get('security.user_password_hasher');

echo "🔧 CRÉATION/MAJ ADMIN AVEC RÔLE...\n";
echo "================================\n\n";

// 1. Vérifier si l'admin existe
$repo = $em->getRepository(User::class);
$admin = $repo->findOneBy(['email' => 'admin@alloambulance.com']);

if ($admin) {
    echo "📋 Admin existe déjà: " . $admin->getEmail() . "\n";
    echo "   Rôles actuels: " . json_encode($admin->getRoles()) . "\n";
    
    // Mettre à jour le mot de passe et ajouter ROLE_ADMIN
    $admin->setPassword($hasher->hashPassword($admin, 'admin123'));
    $admin->setRoles(['ROLE_ADMIN']);
    
    echo "✅ Mot de passe réinitialisé et rôle ajouté\n";
} else {
    echo "🆕 Création nouvel admin...\n";
    
    $admin = new User();
    $admin->setEmail('admin@alloambulance.com');
    $admin->setNom('Admin');
    $admin->setPrenom('System');
    $admin->setTelephone('0600000000');
    $admin->setAdresse('Siège Administratif');
    $admin->setPassword($hasher->hashPassword($admin, 'admin123'));
    $admin->setRoles(['ROLE_ADMIN']); // IMPORTANT: ROLE_ADMIN ici!
    
    echo "✅ Nouvel admin créé avec ROLE_ADMIN\n";
}

$em->persist($admin);
$em->flush();

// Vérification
$verified = $repo->findOneBy(['email' => 'admin@alloambulance.com']);
echo "\n🔍 VÉRIFICATION:\n";
echo "Email: " . $verified->getEmail() . "\n";
echo "Rôles: " . json_encode($verified->getRoles()) . "\n";

if (in_array('ROLE_ADMIN', $verified->getRoles())) {
    echo "✅ ROLE_ADMIN confirmé!\n";
} else {
    echo "❌ ERREUR: ROLE_ADMAN manquant!\n";
}

echo "\n📋 IDENTIFIANTS FINAUX:\n";
echo "=======================\n";
echo "🌐 URL: http://127.0.0.1:8000/admin/login\n";
echo "📧 Email: admin@alloambulance.com\n";
echo "🔑 Mot de passe: admin123\n";
echo "👑 Rôle: ROLE_ADMIN\n";
echo "=======================\n\n";

echo "🎉 Connectez-vous maintenant!\n";