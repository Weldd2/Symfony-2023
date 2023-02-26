<?php

namespace App\Controller;

use App\Entity\Auteur;
use App\Form\AuteurType;
use App\Repository\AuteurRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/auteur')]
class AuteurController extends AbstractController
{

    #[Route('/', name: 'app_auteur_index', methods: ['GET'])]
    public function index(AuteurRepository $auteurRepository, PaginatorInterface $paginator, Request $request): Response
    {
		$auteurs = $paginator->paginate(
            $auteurRepository->findAllWithLivresAndNationalites(),
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('auteur/index.html.twig', [
            'auteurs' => $auteurs,
        ]);
    }

	
	#[Security("is_granted('ROLE_ADMIN')")]
    #[Route('/new', name: 'app_auteur_new', methods: ['GET', 'POST'])]
    public function new(Request $request, AuteurRepository $auteurRepository): Response
    {
        $auteur = new Auteur();
        $form = $this->createForm(AuteurType::class, $auteur);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $auteurRepository->save($auteur, true);

            return $this->redirectToRoute('app_auteur_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('auteur/new.html.twig', [
            'auteur' => $auteur,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_auteur_show', methods: ['GET'])]
    public function show(Auteur $auteur): Response
    {
        return $this->render('auteur/show.html.twig', [
            'auteur' => $auteur,
        ]);
    }

	#[Security("is_granted('ROLE_ADMIN')")]
    #[Route('/{id}/edit', name: 'app_auteur_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Auteur $auteur, AuteurRepository $auteurRepository): Response
    {
        $form = $this->createForm(AuteurType::class, $auteur);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $auteurRepository->save($auteur, true);

            return $this->redirectToRoute('app_auteur_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('auteur/edit.html.twig', [
            'auteur' => $auteur,
            'form' => $form,
        ]);
    }

	#[Security("is_granted('ROLE_ADMIN')")]
    #[Route('/{id}', name: 'app_auteur_delete', methods: ['POST'])]
    public function delete(Request $request, Auteur $auteur, AuteurRepository $auteurRepository): Response
    {
		if(count($auteur->getLivres())>0) {
			$this->addFlash('danger', 'Vous ne pouvez pas supprimer un auteur s\'il a écrit un livre.');
		} else {

			
			if ($this->isCsrfTokenValid('delete'.$auteur->getId(), $request->request->get('_token'))) {
				$auteurRepository->remove($auteur, true);
			}
		}

        return $this->redirectToRoute('app_auteur_index', [], Response::HTTP_SEE_OTHER);
	}

	
}

