<?php

namespace Mr\AventriSdk\Model\Registration;

use Mr\Bootstrap\Model\BaseModel;
use Mr\AventriSdk\Sdk;

class Attendee extends BaseModel
{
    public static function getResource()
    {
        $token = Sdk::getToken();
        return 'https://www.eiseverywhere.com/api/v2/ereg/listAttendees.json';
    }
}
