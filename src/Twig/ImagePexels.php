<?php
// src/Twig/AppExtension.php
namespace App\Twig;

use App\Entity\Checklist;
use App\Entity\MyUser;
use App\Entity\Rooms;
use App\Entity\RoomsUser;
use App\Entity\SchedulingTime;
use App\Entity\SchedulingTimeUser;
use App\Entity\Standort;
use App\Entity\User;
use App\Service\LicenseService;
use App\Service\MessageService;
use App\Service\PexelService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;
use function GuzzleHttp\Psr7\str;

class ImagePexels extends AbstractExtension
{



    public function __construct(private PexelService $pexelsService, private EntityManagerInterface $em, TokenStorageInterface $tokenStorage)
    {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('pexelsImage', [$this, 'pexelsImage']),
        ];
    }
    public function pexelsImage()
    {
       return $this->pexelsService->getImageFromPexels();
    }

}
