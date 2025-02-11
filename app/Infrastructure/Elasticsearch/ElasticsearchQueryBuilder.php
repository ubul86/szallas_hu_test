<?php

namespace App\Infrastructure\Elasticsearch;

use Elastic\Elasticsearch\Client as ElasticClient;
use Http\Promise\Promise;
use InvalidArgumentException;

class ElasticsearchQueryBuilder
{
    protected ElasticClient $elasticsearch;
    protected array $query = [];
    private array $mustConditions = [];
    private array $filterConditions = [];

    public function __construct(ElasticClient $elasticsearch)
    {
        $this->elasticsearch = $elasticsearch;
    }

    public function index(string $index): self
    {
        $this->query['index'] = $index;
        return $this;
    }

    public function save(): ?string
    {
        $response = $this->elasticsearch->index($this->query);

        if ($response instanceof Promise) {
            $response = $response->wait();
        }

        return isset($response['result']) ? $response['result'] : null;
    }

    public function matchAll(): self
    {
        return $this->body([
            'query' => ['match_all' => (object)[]]
        ], true);
    }

    public function match(string $field, string $value): self
    {
        return $this->body([
            'query' => [
                'match' => [$field => $value]
            ]
        ]);
    }

    public function boolQuery(array $must = [], array $filter = [], array $should = [], array $mustNot = []): self
    {
        return $this->body([
            'query' => [
                'bool' => [
                    'must' => $must,
                    'filter' => $filter,
                    'should' => $should,
                    'must_not' => $mustNot
                ]
            ]
        ]);
    }

    public function term(string $field, ?string $value): self
    {
        return $this->body([
            'query' => [
                'term' => [
                    $field => ['value' => $value]
                ]
            ]
        ]);
    }

    public function where(string $field, ?string $value, string $operator = '='): self
    {
        $condition = match ($operator) {
            '=' => ['term' => [$field => $value]],
            '!=' => ['bool' => ['must_not' => ['term' => [$field => $value]]]],
            default => throw new InvalidArgumentException("Unsupported operator: $operator"),
        };

        $this->query['body']['query']['bool']['filter'][] = $condition;
        return $this;
    }

    public function from(int $from): self
    {
        $this->query['body']['from'] = $from;
        return $this;
    }

    public function size(int $size): self
    {
        $this->query['body']['size'] = $size;
        return $this;
    }

    public function sort(string $field, string $order = 'asc'): self
    {
        $this->query['body']['sort'][] = [
            $field => [
                'order' => $order
            ]
        ];
        return $this;
    }

    public function aggregate(string $name, array $aggregation): self
    {
        $this->query['body']['aggs'][$name] = $aggregation;
        return $this;
    }

    public function execute(): array
    {
        $response = $this->elasticsearch->search($this->query);

        if ($response instanceof Promise) {
            $response = $response->wait();
        }

        return isset($response['hits']) ? $response['hits'] : [];
    }

    public function getQuery(): array
    {
        return $this->query;
    }

    public function body(array $body, bool $replaceQuery = false): self
    {
        if ($replaceQuery && isset($body['query'])) {
            $this->query['body']['query'] = $body['query'];
        } else {
            $this->query['body'] = array_merge_recursive($this->query['body'] ?? [], $body);
        }

        return $this;
    }


    public function id(int $id): self
    {
        $this->query['id'] = $id;
        return $this;
    }

    public function indexExists(): bool
    {
        $response =  $this->elasticsearch->indices()->exists([
            'index' => $this->query['index'] ?? null,
        ]);

        if ($response instanceof Promise) {
            $response = $response->wait();
        }

        return $response->getStatusCode() === 200;
    }

    public function delete(): ?string
    {

        $response = $this->elasticsearch->delete($this->query);

        if ($response instanceof Promise) {
            $response = $response->wait();
        }

        return $response['result'] ?? null;
    }

    public function search(): array
    {
        $response = $this->elasticsearch->search($this->query);

        if ($response instanceof Promise) {
            $response = $response->wait();
        }

        return isset($response['hits']['hits']) ? $response['hits']['hits'] : [];
    }

    private function addMustCondition(array $condition): void
    {
        $this->mustConditions[] = $condition;
    }

    public function searchWithWildcard(string $field, string $value, array $params = []): self
    {
        $defaultParams = [
            'boost' => 1.0,
            'rewrite' => 'constant_score'
        ];

        $params = array_merge($defaultParams, $params);

        $wildcardCondition = [
            'wildcard' => [
                $field => array_merge(['value' => '*' . $value . '*'], $params)
            ]
        ];

        $this->addMustCondition($wildcardCondition);

        return $this;
    }


    private function addFilterCondition(array $condition): void
    {
        $this->filterConditions[] = $condition;
    }

    public function applyFilters(array $filters): self
    {
        foreach ($filters as $field => $values) {
            $this->addFilterCondition([
                'terms' => [
                    $field => $values
                ]
            ]);
        }
        return $this;
    }

    public function buildQuery(): self
    {
        if (empty($this->mustConditions) && empty($this->filterConditions)) {
            $this->query['body']['query'] = ['match_all' => new \stdClass()];
        } else {
            $this->query['body']['query']['bool']['must'][]['bool']['should'] = $this->mustConditions;
            $this->query['body']['query']['bool']['filter'] = $this->filterConditions;
        }
        return $this;
    }
}
