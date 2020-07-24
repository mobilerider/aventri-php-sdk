<?php


namespace Mr\AventriSdk\Repository\Registration;

use Mr\AventriSdk\Model\Registration\Attendee;
use Mr\Bootstrap\Http\Filtering\MrApiQueryBuilder;
use Mr\Bootstrap\Interfaces\HttpDataClientInterface;
use Mr\Bootstrap\Repository\BaseRepository;
use Mr\AventriSdk\Sdk;

class AttendeeRepository extends BaseRepository
{
    const ENDPOINT_PREFIX = "ereg/";

    public function __construct(HttpDataClientInterface $client, array $options = [])
    {
        $options["queryBuilderClass"] = MrApiQueryBuilder::class;
        parent::__construct($client, $options);
    }
    
    public function getModelClass()
    {
        return Attendee::class;
    }

    protected function getResourcePath()
    {
        return Sdk::API_VERSION . self::ENDPOINT_PREFIX;
    }

    public function getUri($id = null, $path = '')
    {
        $uri = $this->getResourcePath() . $path;  

        if ($id) {
            return $uri . "getAttendee.json?attendeeid=$id";
        }

        return $uri . "listAttendees.json";
    }

    public function parseOne(array $data, array &$metadata = [])
    {
        return $data;
    }

    public function parseMany(array $data, array &$metadata = [])
    {
        return $data;
    }

    protected function buildQuery(array $filters, array $params)
    {
        return $filters + $params;
    }
}