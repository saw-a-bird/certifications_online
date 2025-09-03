<?php 

namespace App\DataFixtures;

use App\Entity\User;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

use Faker\Generator;
use Faker\Provider\en_US\Person;
use Faker\Provider\Internet;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private $passwordHasher;
    private $faker;

    public function __construct(UserPasswordHasherInterface $passwordHasher, Generator $faker)
    {
        $this->passwordHasher = $passwordHasher;
        $this->faker = $faker;
    }

    public function load(ObjectManager $manager)
    {
        $this->loadUsers($manager);
    }

    private function loadUsers(ObjectManager $manager)
    {
        foreach ($this->getUserData() as [$fullname, $username, $password, $email, $roles]) {
            $user = new User();
            $user->setFullName($fullname);
            $user->setUsername($username);
            $user->setPassword($this->passwordHasher->hashPassword($user, $password));
            $user->setEmail($email);
            $user->setRoles($roles);
            $manager->persist($user);
            $this->addReference($username, $user);
        }
        $manager->flush();
    }

    private function getUserData($quantity = 10): array
    {
        $this->faker->addProvider(new Person($this->faker));
        $this->faker->addProvider(new Internet($this->faker));
        $data = [];
        for($i=0; $i<$quantity ;$i++) {
            array_push($data, [
                $this->faker->name,
                $this->faker->userName,
                'secret',
                $this->faker->safeEmail,
                ['ROLE_USER'],
            ]);
        }
        return $data;
    }
}