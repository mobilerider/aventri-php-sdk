<?php


namespace Mr\AventriSdk\Repository\Registration;

use Mr\AventriSdk\Model\Registration\Attendee;
use Mr\Bootstrap\Http\Filtering\MrApiQueryBuilder;
use Mr\Bootstrap\Interfaces\HttpDataClientInterface;
use Mr\Bootstrap\Repository\BaseRepository;
use Mr\AventriSdk\Sdk;

class AttendeeRepository extends BaseRepository
{
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
        
        return $this->getResource();
    }

    public function parseOne(array $data, array &$metadata = [])
    {
        $metadata = $data['meta'];

        return $data['object'];
    }

    public function parseMany(array $data, array &$metadata = [])
    {
        $metadata = $data;

        return $data;
    }

    protected function buildQuery(array $filters, array $params)
    {
        return $filters + $params;
    }
}