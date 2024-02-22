<?php

namespace App\DataFixtures;

use App\Entity\Character;
use App\Entity\Combo;
use App\Entity\Input;
use App\Entity\ProPlayer;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Generator;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    /**
     * @var Generator
     */
    private Generator $faker;
    /**
     * Password Hasher
     * 
     * @var UserPasswordHasherInterface
     */
    private UserPasswordHasherInterface $userPasswordHasher;

    public function __construct(UserPasswordHasherInterface $userPasswordHasher){
        $this->faker = Factory::create("fr_FR");
        $this->userPasswordHasher = $userPasswordHasher;
    }
    public function load(ObjectManager $manager): void
    {
        $date = new \DateTime();

        // Character
        $characters = [];
        for ($i = 0; $i < 10; $i++) {
            $character = new Character();
            $character->setName($this->faker->name())
                ->setCreatedAt($date)
                ->setUpdatedAt($date);
            $manager->persist($character);
            $characters[] = $character;
        }

        //Public user
        $publicUser = new User();
        $password = $this->faker->password(2,6);
        $publicUser
            ->setUuid($this->faker->name() . "@" . $password)
            ->setPassword($this->userPasswordHasher->hashPassword($publicUser, $password))
            ->setRoles(["ROLE_PUBLIC"])
            ->setCreatedAt($date)
            ->setUpdatedAt($date)
            ->setStatus("on")
            ->setMain($characters[array_rand($characters,1)]);
        $manager->persist($publicUser);

        // User
        for ($i = 0; $i < 10; $i++) {
            $userUser = new User();
            $password = $this->faker->password(2,6);
            $userUser
                ->setUuid($this->faker->name() . "@" . $password)
                ->setPassword($this->userPasswordHasher->hashPassword($userUser, $password))
                ->setRoles(["ROLE_USER"])
                ->setCreatedAt($date)
                ->setUpdatedAt($date)
                ->setStatus("on")
                ->setMain($characters[array_rand($characters,1)]);
            $manager->persist($userUser);
        }
        // Admin user
        $adminUser = new User();
        $adminUser
            ->setUuid("admin")
            ->setPassword($this->userPasswordHasher->hashPassword($adminUser, "password"))
            ->setRoles(["ROLE_ADMIN"])
            ->setCreatedAt($date)
            ->setUpdatedAt($date)
            ->setStatus("on")
            ->setMain($characters[array_rand($characters,1)]);
        $manager->persist($adminUser);

        // Combo
        for ($i = 0; $i < 10; $i++) {
            $combo = new Combo();
            $combo->setName($this->faker->name())
                ->setMain($characters[array_rand($characters,1)])
                ->setCreatedAt($date)
                ->setUpdatedAt($date);
            $manager->persist($combo);    
        }

        // Input
        for ($i = 0; $i < 10; $i++) {
            $input = new Input();
            $input->setInput($this->faker->text(10))
                ->setCreatedAt($date)
                ->setUpdatedAt($date);
            $manager->persist($input);
        }

        // ProPlayer
        for ($i = 0; $i < 10; $i++) {
            $proPlayer = new ProPlayer();
            $proPlayer->setName($this->faker->name())
                ->setMain($characters[array_rand($characters,1)])
                ->setCreatedAt($date)
                ->setUpdatedAt($date);
            $manager->persist($proPlayer);
        }

        $manager->flush();
    }
}
