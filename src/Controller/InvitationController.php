<?php

namespace App\Controller;

use App\Entity\User;
use App\Service\InviteService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

class InvitationController extends AbstractController
{
    /**
     * @Route("/login/invitationAccept/{id}", name= "invitation_accept")
     * @ParamConverter("user", options={"mapping"={"id":"registerId"}})
     */
public function index(InviteService $inviteService, User $user, Request $request): Response
    {

        $inviteService->connectUserWithEmail($user,$this->getUser());
        return $this->redirectToRoute('dashboard');
    }
}
