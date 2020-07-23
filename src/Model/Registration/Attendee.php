<?php

namespace Mr\AventriSdk\Model\Registration;

use Mr\Bootstrap\Model\BaseModel;
use Mr\AventriSdk\Sdk;

class Attendee extends BaseModel
{
    public static function getResource()
    {
       
        return 'attendee';
    }
}
