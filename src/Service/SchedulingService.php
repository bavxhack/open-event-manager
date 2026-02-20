<?php


namespace App\Service;


use App\Entity\SchedulingTime;
use Doctrine\ORM\EntityManagerInterface;

class SchedulingService
{
    public function __construct(private EntityManagerInterface $em, private UserService $userService)
    {
    }
    public function chooseTimeSlot(SchedulingTime $schedulingTime):?bool{
        $room = $schedulingTime->getScheduling()->getRoom();
        $room->setScheduleMeeting(false);
        $room->setStart($schedulingTime->getTime());
        $end = clone $schedulingTime->getTime();
        $end->modify('+'.$room->getDuration().'min');
        $room->setEnddate($end);
        $this->em->persist($room);
        $this->em->flush();
        try {
            foreach ($room->getUser() as $data){
                $this->userService->addUser($data,$room);
            }
        }catch (\Exception $exception){
            return false;
        }
        return true;
    }
}