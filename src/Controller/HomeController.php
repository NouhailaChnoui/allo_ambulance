<?php
namespace App\Controller;

use App\Entity\User;
use App\Entity\Demande;
use App\Form\DemandeType;
use App\Repository\DemandeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(DemandeRepository $demandeRepository): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        if (!$user) {
            return $this->redirectToRoute('app_login');
        }
        $totalDemandes = $demandeRepository->count(['client' => $user]);
        $demandesEnCours = $demandeRepository->count(['client' => $user, 'statut' => 'en_cours']);
        $demandesTerminees = $demandeRepository->count(['client' => $user, 'statut' => 'termine']);

        return $this->render('home/index.html.twig', [
            'totalDemandes' => $totalDemandes,
            'demandesEnCours' => $demandesEnCours,
            'demandesTerminees' => $demandesTerminees,
        ]);
    }
    #[Route('/demande/urgence', name: 'app_demande_urgence')]
    public function urgence(Request $request, EntityManagerInterface $entityManager): Response
    {
        return $this->creerDemande('urgence', $request, $entityManager);
    }

    #[Route('/demande/muti-urgent', name: 'app_demande_muti_urgent')]
    public function mutiUrgent(Request $request, EntityManagerInterface $entityManager): Response
    {
        return $this->creerDemande('muti-urgent', $request, $entityManager);
    }

    #[Route('/demande/transport', name: 'app_demande_transport')]
    public function transport(Request $request, EntityManagerInterface $entityManager): Response
    {
        return $this->creerDemande('transport', $request, $entityManager);
    }

    private function creerDemande(string $type, Request $request, EntityManagerInterface $entityManager): Response
    {
        $demande = new Demande();
        $demande->setType($type);
        $demande->setClient($this->getUser());

        $form = $this->createForm(DemandeType::class, $demande);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($demande);
            $entityManager->flush();

            $this->addFlash('success', 'Demande créée avec succès!');

            return $this->redirectToRoute('app_historique');
        }

        return $this->render('home/demande.html.twig', [
            'form' => $form->createView(),
            'type' => $type,
        ]);
    }

    #[Route('/about', name: 'app_about')]
    public function about(): Response
    {
        return $this->render('home/about.html.twig');
    }

    #[Route('/historique', name: 'app_historique')]
    public function historique(DemandeRepository $demandeRepository): Response
    {
        $demandes = $demandeRepository->findBy(
            ['client' => $this->getUser()],
            ['createdAt' => 'DESC']
        );

        return $this->render('home/historique.html.twig', [
            'demandes' => $demandes,
        ]);
    }

    #[Route('/profil', name: 'app_profil')]
    public function profil(Request $request, EntityManagerInterface $entityManager): Response
    {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        if ($request->isMethod('POST')) {
            $user->setNom($request->request->get('nom'));
            $user->setPrenom($request->request->get('prenom'));
            $user->setTelephone($request->request->get('telephone'));
            $user->setAdresse($request->request->get('adresse'));

            $entityManager->flush();

            $this->addFlash('success', 'Profil mis à jour avec succès!');
        }

        return $this->render('home/profil.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/contact', name: 'app_contact')]
    public function contact(Request $request): Response
    {
        if ($request->isMethod('POST')) {
            $this->addFlash('success', 'Message envoyé avec succès!');
            return $this->redirectToRoute('app_contact');
        }

        return $this->render('home/contact.html.twig');
    }

    #[Route('/demande/{id}/delete', name: 'app_demande_delete', methods: ['POST'])]
    public function deleteDemande(int $id, DemandeRepository $demandeRepository, EntityManagerInterface $entityManager): Response
    {
        $demande = $demandeRepository->find($id);

        if (!$demande || $demande->getClient() !== $this->getUser()) {
            throw $this->createNotFoundException('Demande non trouvée');
        }

        $entityManager->remove($demande);
        $entityManager->flush();

        $this->addFlash('success', 'Demande supprimée avec succès!');

        return $this->redirectToRoute('app_historique');
    }
    #[Route('/demande/{id}', name: 'app_demande_detail')]
    public function demandeDetail(int $id, DemandeRepository $demandeRepository): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        
        $demande = $demandeRepository->find($id);
                if (!$demande || $demande->getClient() !== $user) {
            $this->addFlash('error', 'Demande non trouvée ou accès non autorisé');
            return $this->redirectToRoute('app_historique');
        }
        
        return $this->render('home/demande_detail.html.twig', [
            'demande' => $demande,
        ]);
    }
}
