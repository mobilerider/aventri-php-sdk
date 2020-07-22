<?php

namespace Mr\AventriSdk\Model\Registration;

use Mr\Bootstrap\Model\BaseModel;

class Attendee extends BaseModel
{
    public static function getResource()
    {
        return 'attendee';
    }
}