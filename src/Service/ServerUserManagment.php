<?php


namespace App\Service;


use App\Entity\EmailDomainsToServers;
use App\Entity\KeycloakGroupsToStandorts;
use App\Entity\Standort;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class ServerUserManagment
{

    public function __construct(private ParameterBagInterface $parameter, private EntityManagerInterface $em)
    {
    }

    /**
     * @param User $user
     * @return array
     * Return the Server for an User. This can be
     * individual server
     * default server for all users
     * keycloakserver by group or domain
     */
    public function getStandortsFromUser(User $user)
    {
        $standorts = array();
        //here we add theserver which is directed connected to a user
        $standorts = $user->getStandort()->toArray();


        // here we add the standorts from thekeycloak group
        if ($user->getGroups()) {
            foreach ($user->getGroups() as $data1) {
                $tmpG = $this->em->getRepository(KeycloakGroupsToStandorts::class)->findBy(array('keycloakGroup' => $data1));
                foreach ($tmpG as $data2) {
                    if (!in_array($data2->getStandort(), $standorts)) {
                        $standorts[] = $data2->getStandort();
                    }
                }
            }
        }

        $domain = explode('@', $user->getEmail())[1];
        $tmpE = $this->em->getRepository(KeycloakGroupsToStandorts::class)->findBy(array('keycloakGroup' =>$domain ));
        foreach ($tmpE as $data2) {
            if (!in_array($data2->getServer(), $standorts)) {
                $standorts[] = $data2->getStandort();
            }
        }

        $default = $this->em->getRepository(Standort::class)->find($this->parameter->get('default_jitsi_server_id'));
        //here we add the default group which is set in the env
        if ($default && !in_array($default, $standorts)) {
            $standorts[] = $default;
        }

        return $standorts;
    }
}
