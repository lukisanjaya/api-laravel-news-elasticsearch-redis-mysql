<?php

namespace App\ElasticSearch;
use Elasticsearch\ClientBuilder as ElasticBuilder;

class ElasticDB
{
    protected $connection;

    public function __construct() {
        $this->connection = ElasticBuilder::create()
        ->setHosts([env('ELASTICSEARCH_HOST', 'localhost:9200')])
        ->build();
    }

}
