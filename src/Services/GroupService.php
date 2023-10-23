<?php

namespace App\Services;

use App\Entity\Championship;
use App\Entity\Group;
use App\Repository\GroupRepository;

readonly class GroupService
{

    public function __construct(private GroupRepository $groupRepository)
    {
    }

    public function save(Group $group): Group
    {
        $this->groupRepository->save($group);
        return $group;
    }

    public function makeGroup(string $name, Championship $championship): Group
    {
        $group = new Group;
        $group->setName($name);
        $group->setChampionship($championship);

        $this->groupRepository->save($group);

        return $group;
    }

    public function makeGroupName(int $position): string
    {
        return chr(ord('A') + $position);
    }
}