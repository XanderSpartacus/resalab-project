<?php

namespace App\Controller;

use App\Entity\Resource;
use App\Form\ResourceType;
use App\Repository\CategoryRepository;
use App\Repository\ResourceRepository;
use App\Security\Voter\ResourceVoter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/resource')]
class ResourceController extends AbstractController
{
    #[Route('/', name: 'app_resource_index', methods: ['GET'])]
    public function index(
        Request $request,
        ResourceRepository $resourceRepository,
        CategoryRepository $categoryRepository
    ): Response {
        $categories = $categoryRepository->findAll();
        $selectedCategoryId = $request->query->get('category');

        if($selectedCategoryId){
            $selectedCategory = $categoryRepository->find($selectedCategoryId);
            $resources = $selectedCategory ? $resourceRepository->findByCategory($selectedCategory) : [];
        }else{
            $resources = $resourceRepository->findBy([], ['createdAt' => 'DESC']);
        }

        return $this->render('resource/index.html.twig', [
            'resources' => $resources,
            'categories' => $categories,
            'selectedCategoryId' => $selectedCategoryId
        ]);
    }

    #[Route('/new', name: 'app_resource_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted(ResourceVoter::CREATE);

        $resource = new Resource();
        $form = $this->createForm(ResourceType::class, $resource);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            // Avant de sauvegarder, nous allons définir les dates de création/mise à jour
            $resource->setCreatedAt(new \DateTimeImmutable());
            $resource->setUpdatedAt(new \DateTimeImmutable());

            $entityManager->persist($resource);
            $entityManager->flush();

            $this->addFlash('success', 'La ressource a été créée avec succès.');

            return $this->redirectToRoute('app_resource_index');
        }

        return $this->render('resource/new.html.twig', [
            'resource' => $resource,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_resource_show', methods: ['GET'])]
    public function show(Resource $resource): Response
    {
        $this->denyAccessUnlessGranted(ResourceVoter::VIEW, $resource);

        return $this->render('resource/show.html.twig', [
            'resource' => $resource,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_resource_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Resource $resource, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted(ResourceVoter::EDIT, $resource);

        $form = $this->createForm(ResourceType::class, $resource);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Met à jour la date de modification
            $resource->setUpdatedAt(new \DateTimeImmutable());
            $entityManager->flush(); // Pas besoin de persist car l'entité est déjà gérée par Doctrine

            $this->addFlash('success', 'La ressource a été modifiée avec succès.');

            return $this->redirectToRoute('app_resource_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('resource/edit.html.twig', [
            'resource' => $resource,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_resource_delete', methods: ['POST'])]
    public function delete(Request $request, Resource $resource, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted(ResourceVoter::DELETE, $resource);

        // Vérifie le jeton CSRF pour s'assurer que la requête provient bien de notre application
        if ($this->isCsrfTokenValid('delete'.$resource->getId(), $request->request->get('_token'))) {
            $entityManager->remove($resource); // Marque l'entité pour la suppression
            $entityManager->flush(); // Execute la suppression en base de données
            $this->addFlash('success', 'La ressource a été supprimée avec succès.');
        } else {
            $this->addFlash('error', 'Jeton CSRF invalide. La suppression a été annulée');
        }

        return $this->redirectToRoute('app_resource_index', [], Response::HTTP_SEE_OTHER);
    }
}
