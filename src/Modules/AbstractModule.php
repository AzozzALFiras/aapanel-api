<?php

namespace AzozzALFiras\AAPanelAPI\Modules;

use AzozzALFiras\AAPanelAPI\HttpClient;

abstract class AbstractModule
{
    protected $client;

    public function __construct(HttpClient $client)
    {
        $this->client = $client;
    }
}
