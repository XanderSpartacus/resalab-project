<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Resource;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class ResourceFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $resources = [
            [
                'name' => 'Vidéoprojecteur',
                'description' => 'Vidéoprojecteur Full HD pour présentations.',
                'category' => 'category_0' // Matériel Informatique
            ],
            [
                'name' => 'Caméra HD',
                'description' => 'Caméra haute définition pour enregistrer des expériences.',
                'category' => 'category_0' // Matériel Informatique
            ],
            [
                'name' => 'Microscope optique',
                'description' => 'Microscope de précision pour l\'observation de cellules.',
                'category' => 'category_3' // Équipement de laboratoire
            ],
            [
                'name' => 'Microscope électronique',
                'description' => 'Visualisation à très haute résolution pour recherche avancée.',
                'category' => 'category_3' // Équipement de laboratoire
            ],
            [
                'name' => 'Ordinateur portable',
                'description' => 'PC portable performant pour travaux pratiques et analyses.',
                'category' => 'category_0' // Matériel Informatique
            ],
            [
                'name' => 'Tableau interactif',
                'description' => 'Tableau tactile pour annoter et manipuler des documents.',
                'category' => 'category_1' // Salles de réunion
            ],
            [
                'name' => 'Imprimante 3D',
                'description' => 'Fabrication de prototypes et pièces plastiques.',
                'category' => 'category_3' // Équipement de laboratoire
            ],
            [
                'name' => 'Spectrophotomètre',
                'description' => 'Appareil de mesure d’absorption lumineuse.',
                'category' => 'category_3' // Équipement de laboratoire
            ],
            [
                'name' => 'Centrifugeuse',
                'description' => 'Machine pour séparer des composants par rotation.',
                'category' => 'category_3' // Équipement de laboratoire
            ],
            [
                'name' => 'Hotte aspirante',
                'description' => 'Manipulation de produits chimiques en environnement sécurisé.',
                'category' => 'category_3' // Équipement de laboratoire
            ],
        ];

        foreach ($resources as $resourceData) {
            $resource = new Resource();
            $resource->setName($resourceData['name']);
            $resource->setDescription($resourceData['description']);
            $resource->setCreatedAt(new \DateTimeImmutable());
            $resource->setUpdatedAt(new \DateTimeImmutable());

            // On récupère la réference de la catégorie
            // NOTE : la version 4.x de doctrine/doctrine-fixtures-bundle exige le FQCN en deuxième argument de getReference
            $resource->setCategory($this->getReference($resourceData['category'], Category::class));

            $manager->persist($resource);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            CategoryFixtures::class
        ];
    }
}
