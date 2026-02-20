<?php


namespace App\Service\api;


use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class KeycloakService
{
    public function __construct(private EntityManagerInterface $em)
    {
    }

    public function getUSer($email,$keycloakId = null) :?User{
        $user = null;
        if($keycloakId){
            $user = $this->em->getRepository(User::class)->findOneBy(array('keycloakId'=>$keycloakId));
            if ($user){
                return $user;
            }
        }

        $user = $this->em->getRepository(User::class)->findOneBy(array('email'=>$email));
        return $user;
    }
}