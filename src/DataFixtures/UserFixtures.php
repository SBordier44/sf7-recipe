<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture implements DependentFixtureInterface
{
    public function __construct(private readonly UserPasswordHasherInterface $passwordHasher)
    {
    }

    #[\Override]
    public function load(ObjectManager $manager): void
    {
        // Admin user
        $adminUser = (new User())
            ->setEmail('admin@demo.fr')
            ->setRoles(['ROLE_ADMIN', 'ROLE_USER'])
            ->setPassword($this->passwordHasher->hashPassword(new User(), 'admin'))
            ->setIsVerified(true)
            ->setApiToken('adminApiToken')
            ->setLocale('fr');
        $manager->persist($adminUser);
        $this->addReference('admin_user', $adminUser);

        // Demo users
        for ($i = 1; $i < 11; $i++) {
            $demoUser = (new User())
                ->setEmail("user$i@demo.fr")
                ->setRoles(['ROLE_USER'])
                ->setPassword($this->passwordHasher->hashPassword(new User(), 'demo'))
                ->setIsVerified(array_rand([true, false]))
                ->setLocale('fr');
            $manager->persist($demoUser);
            $this->addReference('user_' . $i, $demoUser);
        }

        $manager->flush();
    }

    #[\Override]
    public function getDependencies(): array
    {
        return [AppFixtures::class];
    }
}
