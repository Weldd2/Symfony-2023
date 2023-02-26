<?php


namespace App\DataFixtures;

use DateTime;
use Faker\Factory;
use App\Entity\User;
use Faker\Generator;
use App\Entity\Livre;
use App\Entity\Auteur;
use App\Entity\Nationalite;
use Doctrine\Persistence\ObjectManager;
use App\Repository\NationaliteRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;


class AppFixtures extends Fixture
{

	private Generator $faker;

	public function __construct()
    {
        $this->faker = Factory::create('fr_FR');
		
    }
	
    public function load(ObjectManager $manager)
	{
		$nationalites = [];
		$auteurs = [];

		//créé les nationalités
		for ($i=0; $i < 142; $i++) { 
			$nationalite = new Nationalite();
			$nationalite->setCode($this->faker->unique->countryCode());
			$manager->persist($nationalite);

			array_push($nationalites, $nationalite);
		}

		// Créer 10 auteurs avec des nationalités aléatoires
		for ($i = 0; $i < 10; $i++) {
			$user = new User();
			$user->setEmail($this->faker->email());
			$ROLES = ['ROLE_USER', 'ROLE_ADMIN'];
			$user->setRoles([$ROLES[rand(0,1)]]);
			$user->setPrenom($this->faker->firstName());
			$user->setNom($this->faker->lastName());
			$user->setPassword('$2y$13$lGGYWzIwwc6NQyPE9/rRSOuDW7dxxCdsxOE7H.comoUavhqxKwQqe');

			$auteur = new Auteur();
			$auteur->setPrenom($this->faker->firstName());
			$auteur->setNom($this->faker->lastName());
			$auteur->setDateNaissance($this->faker->dateTimeBetween('-90 year', '-20 year'));
			

			// Choisir une nationalité aléatoire parmi celles chargées depuis la base de données
			$randomNationalite = $nationalites[array_rand($nationalites)];
			$auteur->setNationalite($randomNationalite);

			$manager->persist($auteur);
			array_push($auteurs, $auteur);
			$manager->persist($user);
		}

		for ($i=0; $i < 10; $i++) { 
			$livre = new Livre();
			
			$livre->setTitre($this->faker->words(rand(1,3), true));
			$livre->setQuatrieme($this->faker->paragraph());
			$livre->setDateParution(new DateTime($this->faker->date()));
			$rand = rand(1, count($auteurs));
			foreach ($auteurs as $key => $auteur) {
				if($key == $rand) {
					$livre->addAuteur($auteur);
				}
			}
			$manager->persist($livre);
		}

		$manager->flush();
	}

}
