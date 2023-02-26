<?php

namespace App\Controller;

use DateTime;
use App\Entity\Livre;
use App\Entity\Emprunt;
use App\Form\LivreType;
use App\Repository\LivreRepository;
use App\Repository\AuteurRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/livre')]
class LivreController extends AbstractController
{
    #[Route('/', name: 'app_livre_index', methods: ['GET'])]
    public function index(LivreRepository $livreRepository, PaginatorInterface $paginator, Request $request): Response
    {
		$livres = $paginator->paginate(
            $livreRepository->findAllLivresWithAuteursAndNationalites(),
            $request->query->getInt('page', 1),
            10
        );


        return $this->render('livre/index.html.twig', [
            'livres' => $livres
        ]);
    }

	#[Security("is_granted('ROLE_ADMIN')")]
    #[Route('/new', name: 'app_livre_new', methods: ['GET', 'POST'])]
    public function new(Request $request, LivreRepository $livreRepository, AuteurRepository $auteurRepository): Response
    {
        $livre = new Livre();
        $form = $this->createForm(LivreType::class, $livre);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

			if( count($livre->getAuteurs()) >3) {
				$this->addFlash('danger', 'Vous ne pouvez pas avoir plus de 3 auteurs pour un livre.');
				return $this->redirectToRoute('app_livre_new', [], Response::HTTP_SEE_OTHER);

			} else {
				foreach ($livre->getAuteurs() as $key => $auteur) {
					$auteur->addLivre($livre);
					$auteurRepository->save($auteur);
				}

				$livreRepository->save($livre, true);
				$this->addFlash('success', "Le livre ".$livre->getTitre()." a bien été ajouté.");
			}

            return $this->redirectToRoute('app_livre_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('livre/new.html.twig', [
            'livre' => $livre,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_livre_show', methods: ['GET'])]
    public function show(Livre $livre): Response
    {
        return $this->render('livre/show.html.twig', [
            'livre' => $livre,
        ]);
    }

	#[Security("is_granted('ROLE_ADMIN')")]
    #[Route('/{id}/edit', name: 'app_livre_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Livre $livre, LivreRepository $livreRepository, AuteurRepository $auteurRepository): Response
    {
        $form = $this->createForm(LivreType::class, $livre);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

			if( count($livre->getAuteurs()) >3) {
				$this->addFlash('danger', 'Vous ne pouvez pas avoir plus de 3 auteurs pour un livre.');
			} else {
				foreach ($livre->getAuteurs() as $key => $auteur) {
					$auteur->addLivre($livre);
					$auteurRepository->save($auteur);
				}
				
				$livreRepository->save($livre, true);
				$this->addFlash('success', "Le livre ".$livre->getTitre()." a bien été modifié.");
			}

            return $this->redirectToRoute('app_livre_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('livre/edit.html.twig', [
            'livre' => $livre,
            'form' => $form,
        ]);
    }

	#[Security("is_granted('ROLE_ADMIN')")]
    #[Route('/{id}', name: 'app_livre_delete', methods: ['POST'])]
    public function delete(Request $request, Livre $livre, LivreRepository $livreRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$livre->getId(), $request->request->get('_token'))) {
            $livreRepository->remove($livre, true);
        }

		$this->addFlash('success', "Le livre ".$livre->getTitre()." a bien été supprimé.");


        return $this->redirectToRoute('app_livre_index', [], Response::HTTP_SEE_OTHER);
    }

	#[Security("is_granted('IS_AUTHENTICATED_FULLY')")]
	#[Route('/emprunt/{id}', name: 'app_livre_emprunt', methods: ['GET'])]
    public function emprunter(Livre $livre, EntityManagerInterface $manager): Response
    {
		$emprunt = new Emprunt();
		$today = new DateTime();
		$user = $this->getUser();
		$oneWeekLater = $today->modify('+1 week');
		$emprunt->setDateEmprunt($today);
		$emprunt->setDateRetour($oneWeekLater);
		$emprunt->setLivre($livre);
		$emprunt->setPrenomEmprunteur($user->getPrenom());
		$emprunt->setNomEmprunteur($user->getNom());
		$livre->addEmprunt($emprunt);

		$manager->persist($livre);
		$manager->persist($emprunt);
		$manager->flush();

		$this->addFlash('success', "Le livre ".$livre->getTitre()." a bien été emprunté.");

        return $this->redirectToRoute('app_livre_index', [], Response::HTTP_SEE_OTHER);
    }


}
