<?php

namespace App\Service;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class UserCreatorService
{
    public function __construct(private EntityManagerInterface $em)
    {
    }

    public function createUser($email, $userName, $firstName = null, $lastName = null, $dryrun = false): User
    {
        $user = $this->em->getRepository(User::class)->findOneBy(['username' => $userName]);
        if (!$user) {
            $user = new User();
            $user->setCreatedAt(new \DateTime())
                ->setUsername($userName)
                ->setLastName($lastName)
                ->setFirstName($firstName)
                ->setEmail($email)
                ->setRegisterId(md5(uniqid('ksdjhfkhsdkjhjksd', true)))
                ->setPassword('123')
                ->setUid(md5(uniqid()));

            if (!$dryrun) {
                $this->em->persist($user);
                $this->em->flush();
            }
        }
        return $user;
    }
}
