<?php

namespace App\DataFixtures;

use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CategoryFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $categories = [
            'Matériel Informatique',
            'Salles de réunion',
            'Logiciels',
            'Équipement de laboratoires'
        ];

        foreach ($categories as $key => $categoryName) {
            $category = new Category();
            $category->setName($categoryName);
            $manager->persist($category);

            // On enregistre une référence à la catégorie pour pouvoir la réutiliser dans d'autres fixtures'
            // addReference permet de donner un nom à chaque objet catégorie que nous créons. Nous pouvons les récupérer plus tard.
            $this->addReference('category_' . $key, $category);
        }

        $manager->flush();
    }
}
