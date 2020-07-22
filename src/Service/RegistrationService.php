<?php

namespace Mr\AventriSdk\Service;

use Mr\Bootstrap\Service\BaseHttpService;
use Mr\Sdk\Repository\Account\AttendeeRepository;

class RegistrationService extends BaseHttpService
{
    /**
     * Returns Attendee by id
     *
     * @param $id
     * @return Attendee
     */
    public function getAttendee($id)
    {
        return $this->getRepository(AttendeeRepository::class)->get($id);
    }

    /**
     * Returns all Attendees matching filters
     *
     * @param array $filters
     * @return array
     */
    public function findAttendees(array $filters = [])
    {
        return $this->getRepository(AttendeeRepository::class)->all($filters);
    }
}