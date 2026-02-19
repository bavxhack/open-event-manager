<?php

namespace App\Controller;

use App\Entity\Rooms;
use App\Entity\User;
use App\Service\IcalService;
use App\Service\LicenseService;
use App\Service\UserService;
use Eluceo\iCal\Component\Calendar;
use Eluceo\iCal\Component\Event;
use Eluceo\iCal\Property\Event\Organizer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Contracts\Cache\ItemInterface;

class IcalController extends AbstractController
{
        #[Route("/ical/{id}", name: "ical")]
    #[MapEntity(expr: "repository.findOneBy({'uid': id})")]

    public function index(User $user, UserService $userService,LicenseService $licenseService, IcalService $icalService): Response
    {

        $response = new Response();
        $response->headers->set('Content-Type', 'text/calendar; charset=utf-8');
        $response->headers->set('Content-Disposition', 'inline; filename="cal.ics"');
        $response->setContent($icalService->getIcal($user));
        return $response;
    }
}
