<?php

namespace App\Controller;

use App\Entity\Rooms;
use App\Entity\Standort;
use App\Service\AdminService;
use Doctrine\DBAL\Types\DateType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class AdminController extends AbstractController
{
        #[Route("/admin/server/{server}", name: "admin_server")]
    #[MapEntity(expr: "repository.findOneBy({'id': server})")]

public function server(Standort $server, AdminService $adminService, HttpClientInterface $httpClient, TranslatorInterface $translator)
    {
        $countPart = 0;
        foreach ($server->getRooms() as $room) {
            $countPart = $countPart + count($room->getUser());
        }

        if ($this->getUser() !== $server->getAdministrator()) {
             return $this->redirectToRoute('dashboard',['snack'=>$translator->trans('Fehler, Der Server wurde nicht gefunden'),'color'=>'danger']);
        }

        $req = $httpClient->request('GET', 'https://api.github.com/repos/H2-invent/jitsi-admin/tags');
        $tags = json_decode($req->getContent(), true);
        $chart = $adminService->createChart($server);

        return $this->render('admin/modalChart.html.twig', [
            'server' => $server,
            'countPart' => $countPart,
            'chart' => $chart,
            'tags' => $tags
        ]);

    }
}
