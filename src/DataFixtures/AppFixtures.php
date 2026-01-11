<?php
namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\Ambulance;
use App\Entity\Driver;
use App\Entity\Demande;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        $admin = new User();
        $admin->setEmail('admin@alloambulance.com');
        $admin->setNom('Admin');
        $admin->setPrenom('System');
        $admin->setTelephone('0600000000');
        $admin->setAdresse('Admin Headquarters');
        $admin->setPassword($this->passwordHasher->hashPassword($admin, 'admin123')); 
        $admin->setRoles(['ROLE_ADMIN']);
        $manager->persist($admin);

        $users = [];
        for ($i = 1; $i <= 5; $i++) {
            $user = new User();
            $user->setEmail("user{$i}@example.com");
            $user->setNom("Nom{$i}");
            $user->setPrenom("Prenom{$i}");
            $user->setTelephone('060000000' . $i);
            $user->setAdresse("{$i} Rue des Utilisateurs, Paris");
            $user->setPassword($this->passwordHasher->hashPassword($user, 'password123'));
            $user->setRoles([]); 
            $manager->persist($user);
            $users[] = $user;
        }
        $ambulances = [];
        $types = ['urgence', 'transport', 'multi'];
        $marques = ['Mercedes', 'Renault', 'Volkswagen'];
        $modeles = ['Sprinter', 'Master', 'Transporter'];

        for ($i = 1; $i <= 6; $i++) {
            $ambulance = new Ambulance();
            $ambulance->setImmatriculation('AB-' . str_pad($i, 3, '0', STR_PAD_LEFT) . '-CD');
            $ambulance->setMarque($marques[array_rand($marques)]);
            $ambulance->setModele($modeles[array_rand($modeles)]);
            $ambulance->setType($types[array_rand($types)]);
            $ambulance->setDisponible(true);
            $manager->persist($ambulance);
            $ambulances[] = $ambulance;
        }
        $drivers = [];
        $driverNames = [
            ['Jean', 'Dupont'], ['Marie', 'Martin'], ['Pierre', 'Durand'],
            ['Sophie', 'Dubois'], ['Luc', 'Leroy']
        ];

        foreach ($driverNames as $index => $names) {
            $driver = new Driver();
            $driver->setPrenom($names[0]);
            $driver->setNom($names[1]);
            $driver->setTelephone('06123456' . $index);
            $driver->setAdresse(($index + 1) . " Rue des Chauffeurs, Paris");
            $driver->setPermis('PERMIS-' . str_pad($index + 1, 4, '0', STR_PAD_LEFT));
            $driver->setDisponible(true);
            $manager->persist($driver);
            $drivers[] = $driver;
        }
        $statuts = ['en_attente', 'en_cours', 'termine'];
        $adresses = ['Paris', 'Lyon', 'Marseille', 'Toulouse', 'Nice'];

        for ($i = 1; $i <= 15; $i++) {
            $demande = new Demande();
            $demande->setClient($users[array_rand($users)]);
            $demande->setType($types[array_rand($types)]);
            $demande->setAdresseDepart($adresses[array_rand($adresses)]);
            $demande->setAdresseDestination($adresses[array_rand($adresses)]);
            $demande->setNombrePatients(rand(1, 3));
            $demande->setStatut($statuts[array_rand($statuts)]);

            if (rand(0, 1)) {
                $demande->setAmbulance($ambulances[array_rand($ambulances)]);
            }

            if (rand(0, 1)) {
                $demande->setDriver($drivers[array_rand($drivers)]);
            }

            $demande->setNotes(rand(0, 1) ? 'Note pour la demande ' . $i : null);
            $manager->persist($demande);
        }

        $manager->flush();
    }
}
