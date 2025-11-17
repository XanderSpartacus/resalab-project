<?php

namespace App\Controller\Admin;

use App\Entity\Resource;
use App\Form\ResourceType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ResourceController extends AbstractController
{
    #[Route('/new', name: 'admin_resource_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
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

            return $this->redirectToRoute('app_main');
        }

        return $this->render('admin/resource/new.html.twig', [
            'resource' => $resource,
            'form' => $form,
        ]);
    }
}
