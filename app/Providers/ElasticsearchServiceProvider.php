<?php

namespace App\Providers;

use Elastic\Elasticsearch\Client;
use Elastic\Elasticsearch\ClientBuilder;
use Illuminate\Support\ServiceProvider;

class ElasticsearchServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(Client::class, function () {
            $client = ClientBuilder::create()
                ->setHosts(config('elasticsearch.hosts'));
            $elasticUser = env('ELASTICSEARCH_USER');
            $elasticPassword = env('ELASTICSEARCH_PASS');
            if ($elasticUser && $elasticPassword) {
                $client->setBasicAuthentication($elasticUser, $elasticPassword);
            }

            return $client->build();
        });
    }
}
