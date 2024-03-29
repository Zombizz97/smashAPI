<?php

namespace App\DataFixtures;

use App\Entity\Character;
use App\Entity\Combo;
use App\Entity\Input;
use App\Entity\Order;
use App\Entity\PayloadType;
use App\Entity\ProPlayer;
use App\Entity\User;
use App\Repository\ComboRepository;
use App\Repository\InputRepository;
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

    public function __construct(UserPasswordHasherInterface $userPasswordHasher, ComboRepository $comboRepository, InputRepository $inputRepository){
        $this->faker = Factory::create("fr_FR");
        $this->userPasswordHasher = $userPasswordHasher;
    }
    public function load(ObjectManager $manager): void
    {
        $comboList = [
            "Up Tilt",
            "Up Air",
            "Up Smash",
            "Back Air",
            "Up B",
        ];
        $inputList = [
            "Haut",
            "Avant",
            "Arrière",
            "A",
            "B",
            "Saut",
            "C Stick Haut"
        ];
        $proPlayerList = [
            "Kurama",
            "Dark Wizzy",
            "Nao"
        ];
        $date = new \DateTime();
        $characters = [];

        // Character
        $character = new Character();
        $character->setName("Donkey Kong")
            ->setCreatedAt($date)
            ->setUpdatedAt($date);
        $characters[] = $character;
        $manager->persist($character);

        // Character
        $character = new Character();
        $character->setName("Mario")
            ->setCreatedAt($date)
            ->setUpdatedAt($date);
        $characters[] = $character;
        $manager->persist($character);

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
            ->setMain($character);
        $manager->persist($publicUser);

        // User
        $userUser = new User();
        $password = $this->faker->password(2,6);
        $userUser
            ->setUuid($this->faker->name() . "@" . $password)
            ->setPassword($this->userPasswordHasher->hashPassword($userUser, $password))
            ->setRoles(["ROLE_USER"])
            ->setCreatedAt($date)
            ->setUpdatedAt($date)
            ->setStatus("on")
            ->setMain($character);
        $manager->persist($userUser);
        // Admin user
        $adminUser = new User();
        $adminUser
            ->setUuid("admin")
            ->setPassword($this->userPasswordHasher->hashPassword($adminUser, "password"))
            ->setRoles(["ROLE_ADMIN"])
            ->setCreatedAt($date)
            ->setUpdatedAt($date)
            ->setStatus("on")
            ->setMain($character);
        $manager->persist($adminUser);

        // Combo
        $combos = [];
        for ($i = 0; $i < count($comboList); $i++) {
            $combo = new Combo();
            $combo->setName($comboList[$i])
                ->setCreatedAt($date)
                ->setUpdatedAt($date);
            $manager->persist($combo);
            $combos[] = $combo;    
        }

        // Input
        $inputs = [];
        for ($i = 0; $i < count($inputList); $i++) {
            $input = new Input();
            $input->setInput($inputList[$i])
                ->setCreatedAt($date)
                ->setUpdatedAt($date);
            $manager->persist($input);
            $inputs[] = $input;
        }

        // ProPlayer
        for ($i = 0; $i < count($proPlayerList); $i++) {
            $proPlayer = new ProPlayer();
            $proPlayer->setName($proPlayerList[$i])
                ->setMain($character)
                ->setCreatedAt($date)
                ->setUpdatedAt($date);
            $manager->persist($proPlayer);
        }

        $manager->flush();

        // Order
        $order = new Order();
        $order->setSequence($combos[2]->getId())
            ->setType("comboStarter")
            ->setMain($characters[0]);
        $manager->persist($order);

        $order = new Order();
        $order->setSequence($inputs[0]->getId())
            ->setType("input")
            ->setMain($characters[0]);
        $manager->persist($order);
        
        $order = new Order();
        $order->setSequence($inputs[3]->getId())
            ->setType("input")
            ->setMain($characters[0]);
        $manager->persist($order);
        
        $order = new Order();
        $order->setSequence($combos[0]->getId())
            ->setType("combo")
            ->setMain($characters[0]);
        $manager->persist($order);
        
        $order = new Order();
        $order->setSequence($inputs[6]->getId())
            ->setType("input")
            ->setMain($characters[0]);
        $manager->persist($order);
        
        $order = new Order();
        $order->setSequence($inputs[5]->getId())
            ->setType("inputInCombo")
            ->setMain($characters[0]);
        $manager->persist($order);
        
        $order = new Order();
        $order->setSequence($combos[1]->getId())
            ->setType("combo")
            ->setMain($characters[0]);
        $manager->persist($order);
        
        $order = new Order();
        $order->setSequence($inputs[0]->getId())
            ->setType("input")
            ->setMain($characters[0]);
        $manager->persist($order);
        
        $order = new Order();
        $order->setSequence($inputs[3]->getId())
            ->setType("input")
            ->setMain($characters[0]);
        $manager->persist($order);
        
        $order = new Order();
        $order->setSequence($combos[1]->getId())
            ->setType("combo")
            ->setMain($characters[0]);
        $manager->persist($order);
        
        $order = new Order();
        $order->setSequence($inputs[0]->getId())
            ->setType("input")
            ->setMain($characters[0]);
        $manager->persist($order);
        
        $order = new Order();
        $order->setSequence($inputs[3]->getId())
            ->setType("input")
            ->setMain($characters[0]);
        $manager->persist($order);
        
        $order = new Order();
        $order->setSequence($combos[4]->getId())
            ->setType("combo")
            ->setMain($characters[0]);
        $manager->persist($order);
        
        $order = new Order();
        $order->setSequence($inputs[0]->getId())
            ->setType("input")
            ->setMain($characters[0]);
        $manager->persist($order);
        
        $order = new Order();
        $order->setSequence($inputs[4]->getId())
            ->setType("input")
            ->setMain($characters[0]);
        $manager->persist($order);
        
        // New combo
        $order = new Order();
        $order->setSequence($combos[0]->getId())
            ->setType("comboStarter")
            ->setMain($characters[0]);
        $manager->persist($order);
        
        $order = new Order();
        $order->setSequence($inputs[6]->getId())
            ->setType("input")
            ->setMain($characters[0]);
        $manager->persist($order);
        
        $order = new Order();
        $order->setSequence($inputs[5]->getId())
            ->setType("inputInCombo")
            ->setMain($characters[0]);
        $manager->persist($order);
        
        $order = new Order();
        $order->setSequence($combos[1]->getId())
            ->setType("combo")
            ->setMain($characters[0]);
        $manager->persist($order);
        
        $order = new Order();
        $order->setSequence($inputs[0]->getId())
            ->setType("input")
            ->setMain($characters[0]);
        $manager->persist($order);
        
        $order = new Order();
        $order->setSequence($inputs[3]->getId())
            ->setType("input")
            ->setMain($characters[0]);
        $manager->persist($order);
        
        $order = new Order();
        $order->setSequence($combos[4]->getId())
            ->setType("combo")
            ->setMain($characters[0]);
        $manager->persist($order);
        
        $order = new Order();
        $order->setSequence($inputs[0]->getId())
            ->setType("input")
            ->setMain($characters[0]);
        $manager->persist($order);
        
        $order = new Order();
        $order->setSequence($inputs[4]->getId())
            ->setType("input")
            ->setMain($characters[0]);
        $manager->persist($order);
        
        // New combo
        $order = new Order();
        $order->setSequence($combos[0]->getId())
            ->setType("comboStarter")
            ->setMain($characters[1]);
        $manager->persist($order);
        
        $order = new Order();
        $order->setSequence($inputs[6]->getId())
            ->setType("input")
            ->setMain($characters[1]);
        $manager->persist($order);
        
        $order = new Order();
        $order->setSequence($inputs[5]->getId())
            ->setType("inputInCombo")
            ->setMain($characters[1]);
        $manager->persist($order);
        
        $order = new Order();
        $order->setSequence($combos[1]->getId())
            ->setType("combo")
            ->setMain($characters[1]);
        $manager->persist($order);
        
        $order = new Order();
        $order->setSequence($inputs[0]->getId())
            ->setType("input")
            ->setMain($characters[1]);
        $manager->persist($order);
        
        $order = new Order();
        $order->setSequence($inputs[3]->getId())
            ->setType("input")
            ->setMain($characters[1]);
        $manager->persist($order);
        
        $order = new Order();
        $order->setSequence($combos[4]->getId())
            ->setType("combo")
            ->setMain($characters[1]);
        $manager->persist($order);
        
        $order = new Order();
        $order->setSequence($inputs[0]->getId())
            ->setType("input")
            ->setMain($characters[1]);
        $manager->persist($order);
        
        $order = new Order();
        $order->setSequence($inputs[4]->getId())
            ->setType("input")
            ->setMain($characters[1]);
        $manager->persist($order);
        
        $manager->flush();
    }
}
