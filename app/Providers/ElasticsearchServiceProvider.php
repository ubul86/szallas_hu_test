<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Elastic\Elasticsearch\ClientBuilder;

class ElasticsearchServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton('elasticsearch', function () {
            return ClientBuilder::create()
                ->setHosts(config('elasticsearch.hosts'))
                ->build();
        });
    }
}
