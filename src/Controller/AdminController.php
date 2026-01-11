<?php

namespace App\Controller;

use App\Entity\Demande;
use App\Entity\Ambulance;
use App\Entity\Driver;
use App\Entity\User;
use App\Repository\DemandeRepository;
use App\Repository\AmbulanceRepository;
use App\Repository\DriverRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
class AdminController extends AbstractController
{
    #[Route('/admin/dashboard', name: 'admin_dashboard')]
    public function dashboard(
        DemandeRepository $demandeRepository,
        AmbulanceRepository $ambulanceRepository,
        DriverRepository $driverRepository,
        UserRepository $userRepository
    ): Response {
    $user = $this->getUser();
    if (!$this->isGranted('ROLE_ADMIN')) {
        $this->addFlash('error', 'Accès refusé. Droits administrateur requis.');
        return $this->redirectToRoute('app_home');
    }
    
        $stats = [
            'totalDemandes' => $demandeRepository->count([]),
            'demandesEnCours' => $demandeRepository->count(['statut' => 'en_cours']),
            'demandesTerminees' => $demandeRepository->count(['statut' => 'termine']),
            'ambulancesDisponibles' => $ambulanceRepository->count(['disponible' => true]),
            'chauffeursDisponibles' => $driverRepository->count(['disponible' => true]),
            'totalClients' => $userRepository->count([]) - 1, // Exclure admin
        ];

        $recentDemandes = $demandeRepository->findBy([], ['createdAt' => 'DESC'], 10);
        $ambulances = $ambulanceRepository->findAll();
        $drivers = $driverRepository->findAll();

        return $this->render('admin/dashboard.html.twig', [
            'stats' => $stats,
            'recentDemandes' => $recentDemandes,
            'ambulances' => $ambulances,
            'drivers' => $drivers,
        ]);
    }

    #[Route('/admin/ambulances', name: 'admin_ambulances')]
    public function ambulances(AmbulanceRepository $ambulanceRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $ambulances = $ambulanceRepository->findAll();

        return $this->render('admin/ambulances.html.twig', [
            'ambulances' => $ambulances,
        ]);
    }

    #[Route('/admin/chauffeurs', name: 'admin_chauffeurs')]
    public function chauffeurs(DriverRepository $driverRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $chauffeurs = $driverRepository->findAll();

        return $this->render('admin/chauffeurs.html.twig', [
            'chauffeurs' => $chauffeurs,
        ]);
    }

    #[Route('/admin/clients', name: 'admin_clients')]
    public function clients(UserRepository $userRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $clients = $userRepository->findAll();

        return $this->render('admin/clients.html.twig', [
            'clients' => $clients,
        ]);
    }

    #[Route('/admin/ambulances/new', name: 'admin_ambulance_new', methods: ['POST'])]
    public function newAmbulance(Request $request, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        
        $ambulance = new Ambulance();
        $ambulance->setImmatriculation($request->request->get('immatriculation'));
        $ambulance->setMarque($request->request->get('marque'));
        $ambulance->setModele($request->request->get('modele'));
        $ambulance->setType($request->request->get('type'));
        $ambulance->setDisponible($request->request->get('disponible') === 'on');
        
        $entityManager->persist($ambulance);
        $entityManager->flush();
        
        $this->addFlash('success', 'Ambulance ajoutée avec succès!');
        return $this->redirectToRoute('admin_ambulances');
    }

    #[Route('/admin/ambulances/{id}/edit', name: 'admin_ambulance_edit', methods: ['POST'])]
    public function editAmbulance(int $id, Request $request, EntityManagerInterface $entityManager, AmbulanceRepository $ambulanceRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        
        $ambulance = $ambulanceRepository->find($id);
        if (!$ambulance) {
            throw $this->createNotFoundException('Ambulance non trouvée');
        }
        
        $ambulance->setImmatriculation($request->request->get('immatriculation'));
        $ambulance->setMarque($request->request->get('marque'));
        $ambulance->setModele($request->request->get('modele'));
        $ambulance->setType($request->request->get('type'));
        $ambulance->setDisponible($request->request->get('disponible') === 'on');
        
        $entityManager->flush();
        
        $this->addFlash('success', 'Ambulance modifiée avec succès!');
        return $this->redirectToRoute('admin_ambulances');
    }

    #[Route('/admin/ambulances/{id}/toggle', name: 'admin_ambulance_toggle', methods: ['POST'])]
    public function toggleAmbulance(
        int $id, 
        EntityManagerInterface $entityManager, 
        AmbulanceRepository $ambulanceRepository
    ): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        
        $ambulance = $ambulanceRepository->find($id);
        if (!$ambulance) {
            throw $this->createNotFoundException('Ambulance non trouvée');
        }
        
        $ambulance->setDisponible(!$ambulance->isDisponible());
        $entityManager->flush();
        
        $this->addFlash('success', 'Statut de l\'ambulance modifié!');
        return $this->redirectToRoute('admin_ambulances');
    }


    #[Route('/admin/ambulances/{id}/delete', name: 'admin_ambulance_delete', methods: ['POST'])]
    public function deleteAmbulance(int $id, EntityManagerInterface $entityManager, AmbulanceRepository $ambulanceRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        
        $ambulance = $ambulanceRepository->find($id);
        if (!$ambulance) {
            throw $this->createNotFoundException('Ambulance non trouvée');
        }
        
        $entityManager->remove($ambulance);
        $entityManager->flush();
        
        $this->addFlash('success', 'Ambulance supprimée avec succès!');
        return $this->redirectToRoute('admin_ambulances');
    }


    #[Route('/admin/chauffeurs/new', name: 'admin_chauffeur_new', methods: ['POST'])]
    public function newChauffeur(Request $request, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        
        $driver = new Driver();
        $driver->setNom($request->request->get('nom'));
        $driver->setPrenom($request->request->get('prenom'));
        $driver->setTelephone($request->request->get('telephone'));
        $driver->setAdresse($request->request->get('adresse'));
        $driver->setPermis($request->request->get('permis'));
        $driver->setDisponible($request->request->get('disponible') === 'on');
        
        $entityManager->persist($driver);
        $entityManager->flush();
        
        $this->addFlash('success', 'Chauffeur ajouté avec succès!');
        return $this->redirectToRoute('admin_chauffeurs');
    }
    #[Route('/admin/drivers/{id}/edit', name: 'admin_driver_edit', methods: ['POST'])]
   #[Route('/admin/chauffeurs/{id}/edit', name: 'admin_chauffeur_edit', methods: ['POST'])]
    public function editChauffeur(
        Request $request, 
        int $id, 
        EntityManagerInterface $entityManager, 
        DriverRepository $driverRepository
    ): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        
        $driver = $driverRepository->find($id);
        if (!$driver) {
            throw $this->createNotFoundException('Chauffeur non trouvé');
        }
        
        $driver->setNom($request->request->get('nom'));
        $driver->setPrenom($request->request->get('prenom'));
        $driver->setTelephone($request->request->get('telephone'));
        $driver->setAdresse($request->request->get('adresse'));
        $driver->setPermis($request->request->get('permis'));
        $driver->setDisponible($request->request->get('disponible') === 'on');
        
        $entityManager->flush();
        
        $this->addFlash('success', 'Chauffeur modifié avec succès!');
        return $this->redirectToRoute('admin_chauffeurs');
    }
    #[Route('/admin/drivers/{id}/toggle', name: 'admin_driver_toggle', methods: ['POST'])]
    #[Route('/admin/chauffeurs/{id}/toggle', name: 'admin_chauffeur_toggle', methods: ['POST'])]
    public function toggleChauffeur(
        int $id, 
        EntityManagerInterface $entityManager, 
        DriverRepository $driverRepository
    ): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        
        $driver = $driverRepository->find($id);
        if (!$driver) {
            throw $this->createNotFoundException('Chauffeur non trouvé');
        }
        
        $driver->setDisponible(!$driver->isDisponible());
        $entityManager->flush();
        
        $this->addFlash('success', 'Statut du chauffeur modifié!');
        return $this->redirectToRoute('admin_chauffeurs');
    }
    #[Route('/admin/drivers/{id}/delete', name: 'admin_driver_delete', methods: ['POST'])]
    #[Route('/admin/chauffeurs/{id}/delete', name: 'admin_chauffeur_delete', methods: ['POST'])]
    public function deleteChauffeur(
        int $id, 
        EntityManagerInterface $entityManager, 
        DriverRepository $driverRepository,
        DemandeRepository $demandeRepository
    ): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        
        $driver = $driverRepository->find($id);
        if (!$driver) {
            throw $this->createNotFoundException('Chauffeur non trouvé');
        }
        
        $demandesAssociees = $demandeRepository->findBy(['driver' => $driver]);
        
        if (!empty($demandesAssociees)) {
            $this->addFlash('error', sprintf(
                'Impossible de supprimer le chauffeur %s %s car il est associé à %d demande(s).',
                $driver->getPrenom(),
                $driver->getNom(),
                count($demandesAssociees)
            ));
            return $this->redirectToRoute('admin_chauffeurs');
        }
        
        try {
            $entityManager->remove($driver);
            $entityManager->flush();
            
            $this->addFlash('success', 'Chauffeur supprimé avec succès!');
        } catch (\Exception $e) {
            $this->addFlash('error', 'Erreur lors de la suppression du chauffeur: ' . $e->getMessage());
        }
        
        return $this->redirectToRoute('admin_chauffeurs');
    }
    #[Route('/admin/demandes', name: 'admin_demandes')]
    public function demandes(
        DemandeRepository $demandeRepository,
        AmbulanceRepository $ambulanceRepository,
        DriverRepository $driverRepository,
        Request $request
    ): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $statut = $request->query->get('statut');
        $type = $request->query->get('type');
        $dateDebut = $request->query->get('date_debut');
        $dateFin = $request->query->get('date_fin');

        $demandes = $demandeRepository->findWithFilters($statut, $type, $dateDebut, $dateFin);

        return $this->render('admin/demandes.html.twig', [
            'demandes' => $demandes,
            'ambulances' => $ambulanceRepository->findAll(),
            'chauffeurs' => $driverRepository->findAll(),
        ]);
    }

    #[Route('/admin/demandes/{id}/edit', name: 'admin_demande_edit', methods: ['POST'])]
    public function editDemande(
        int $id,
        Request $request,
        EntityManagerInterface $entityManager,
        DemandeRepository $demandeRepository,
        AmbulanceRepository $ambulanceRepository,
        DriverRepository $driverRepository
    ): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        
        $demande = $demandeRepository->find($id);
        if (!$demande) {
            throw $this->createNotFoundException('Demande non trouvée');
        }
        
        $statut = $request->request->get('statut');
        $ambulanceId = $request->request->get('ambulance_id');
        $driverId = $request->request->get('driver_id');
        
        $demande->setStatut($statut);
        
        if ($ambulanceId) {
            $ambulance = $ambulanceRepository->find($ambulanceId);
            $demande->setAmbulance($ambulance);
            
            if ($statut === 'en_cours' && $ambulance->isDisponible()) {
                $ambulance->setDisponible(false);
            }
        }
        
        if ($driverId) {
            $driver = $driverRepository->find($driverId);
            $demande->setDriver($driver);
            
            if ($statut === 'en_cours' && $driver->isDisponible()) {
                $driver->setDisponible(false);
            }
        }
        
        $entityManager->flush();
        
        $this->addFlash('success', 'Demande modifiée avec succès!');
        return $this->redirectToRoute('admin_demandes');
    }

    #[Route('/admin/demandes/{id}/update-statut/{statut}', name: 'admin_demande_update_statut', methods: ['POST'])]
    public function updateStatutDemande(
        int $id,
        string $statut,
        EntityManagerInterface $entityManager,
        DemandeRepository $demandeRepository
    ): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        
        $demande = $demandeRepository->find($id);
        if (!$demande) {
            throw $this->createNotFoundException('Demande non trouvée');
        }
        
        $demande->setStatut($statut);
        $entityManager->flush();
        
        $this->addFlash('success', 'Statut de la demande mis à jour!');
        return $this->redirectToRoute('admin_demandes');
    }

    #[Route('/admin/demandes/{id}/delete', name: 'admin_demande_delete', methods: ['POST'])]
    public function deleteDemande(
        int $id,
        EntityManagerInterface $entityManager,
        DemandeRepository $demandeRepository
    ): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        
        $demande = $demandeRepository->find($id);
        if (!$demande) {
            throw $this->createNotFoundException('Demande non trouvée');
        }
        
        $entityManager->remove($demande);
        $entityManager->flush();
        
        $this->addFlash('success', 'Demande supprimée avec succès!');
        return $this->redirectToRoute('admin_demandes');
    }
}