<?php
/**
 * Created by PhpStorm.
 * User: Emanuel
 * Date: 03.10.2019
 * Time: 19:01
 */

namespace App\Service;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;


class InviteService
{


    public function __construct(private Environment $twig, private ParameterBagInterface $parameterBag, private MailerService $mailer, private EntityManagerInterface $em, private TranslatorInterface $translator, private UrlGeneratorInterface $router)
    {
    }

    public function newUser($email)
    {
        $user = $this->em->getRepository(User::class)->findOneBy(array('email' => $email));
        if (!$user) {
            $user = new User();
            $user->setLastName('')
                ->setFirstName('')
                ->setCreatedAt(new \DateTime())
                ->setRegisterId(md5(uniqid('ksdjhfkhsdkjhjksd', true)))
                ->setUsername($email)
                ->setEmail($email)
                ->setPassword('123')
                ->setUuid('123');
            $user->setEmail($email);
            $this->em->persist($user);
            $this->em->flush();
        }
        return $user;
    }

    public function connectUserWithEmail(User $userfromregisterId, User $user)
    {
        if (!$user->getTeam()) {
            $user->setTeam($userfromregisterId->getTeam());
        }
        if (!$user->getAkademieUser()) {
            $user->setAkademieUser($userfromregisterId->getAkademieUser());
        }
        foreach ($user->getTeamDsb() as $data) {
            $user->addTeamDsb($data);
        }
        $this->em->persist($user);
        $this->em->remove($userfromregisterId);
        $this->em->flush();
        return $user;
    }

}
