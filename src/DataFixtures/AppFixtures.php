<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\User;
use App\Entity\Offre;
use App\Entity\Client;
use App\Entity\Transaction;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = \Faker\Factory::create('fr_FR');

        // Administateur
        $admin = new User();
        $admin->setEmail('admin@admin.fr')
            ->setPassword('$2y$13$uL1j7kIasILs4c7W4AWevOMO.PbwtANAzR1i5YkKrazZ0sL.KLS8K')
            ->setRoles(['ROLE_ADMIN'])
            ;
        $manager->persist($admin);

        // Membre de l'équipe
        $membre = new User();
        $membre->setEmail('user@user.fr')
            ->setPassword('$2y$13$uL1j7kIasILs4c7W4AWevOMO.PbwtANAzR1i5YkKrazZ0sL.KLS8K')
            ->setRoles(['ROLE_USER'])
            ;

        // Clients de l'agence
        $clients = [];

        for ($i = 0; $i < 20; $i++) {
            $client = new Client();
            $client->setNom($faker->lastName())
                ->setPrenom($faker->firstName())
                ->setTelephone($faker->phoneNumber())
                ->setEmail($faker->email())
                ->setAdresse($faker->streetAddress())
                ->setCp($faker->postcode())
                ->setVille($faker->city())
                ->setPays($faker->country())
                ->setStatut($faker->boolean(60))
                ->setCreatedAt($faker->dateTimeBetween('-6 months'))
                ->setUpdatedAt($faker->dateTimeBetween('-5 months'))
                ;
            $manager->persist($client);
            array_push($clients, $client);
        }

        // Offres de l'agence
        $offres = [
            'Optimisation LinkedIn' => [
                'description' => 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Quisquam, voluptatum.',
                'montant' => 1000,
                'fichier' => 'offre1.pdf'
            ],
            'Site Web' => [
                'description' => 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Quisquam, voluptatum.',
                'montant' => 5000,
                'fichier' => 'offre2.pdf'
            ],
            'Référencement SEO Local' => [
                'description' => 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Quisquam, voluptatum.',
                'montant' => 2000,
                'fichier' => 'offre3.pdf'
            ],
            'Montage Vidéo' => [
                'description' => 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Quisquam, voluptatum.',
                'montant' => 5000,
                'fichier' => 'offre4.pdf'
            ],
            'Campagne Google Ads' => [
                'description' => 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Quisquam, voluptatum.',
                'montant' => 3000,
                'fichier' => 'offre5.pdf'
            ],
            'Campagne Facebook Ads' => [
                'description' => 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Quisquam, voluptatum.',
                'montant' => 3000,
                'fichier' => 'offre6.pdf'
            ]
        ];

        $offresArray = [];

        foreach ($offres as $titre => $data) {
            $offre = new Offre();
            $offre->setTitre($titre)
                ->setDescription($data['description'])
                ->setMontant($data['montant'])
                ->setFichier($data['fichier'])
                ->setCreatedAt($faker->dateTimeBetween('-6 months'))
                ->setUpdatedAt($faker->dateTimeBetween('-5 months'))
                ;
            $manager->persist($offre);
            array_push($offresArray, $offre);
        }

        // Ajout des offres aux clients
        foreach ($clients as $client) {
            if($client->isStatut()) {
                $client->addOffre($faker->randomElement($offresArray));
                $client->addOffre($faker->randomElement($offresArray));
            }
        }

        // Transactions
        for ($i = 0; $i < 20; $i++) {
            $date = $faker->dateTimeBetween('-6 months');

            $transaction = new Transaction();
            $transaction->setClient($faker->randomElement($clients))
                ->setMontant($faker->randomElement([1000, 2000, 3000, 5000]))
                ->setStatut('Payé')
                ->setDate($date)
                ->setCreatedAt($date)
                ->setUpdatedAt($date)
                ;
            $manager->persist($transaction);
        }

        $manager->flush();
    }
}
