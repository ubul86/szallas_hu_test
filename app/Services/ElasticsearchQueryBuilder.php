<?php

namespace App\Services;

use Elastic\Elasticsearch\Client as ElasticClient;

class ElasticsearchQueryBuilder
{
    protected ElasticClient $elasticsearch;
    protected array $query = [];

    public function __construct(ElasticClient $elasticsearch)
    {
        $this->elasticsearch = $elasticsearch;
    }

    public function index(string $index): self
    {
        $this->query['index'] = $index;
        return $this;
    }

    public function save(): array
    {
        return $this->elasticsearch->index($this->query)->asArray();
    }

    public function matchAll(): self
    {
        return $this->body([
            'query' => ['match_all' => (object)[]]
        ]);
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

    public function term(string $field, $value): self
    {
        return $this->body([
            'query' => [
                'term' => [
                    $field => ['value' => $value]
                ]
            ]
        ]);
    }

    public function where(string $field, $value, string $operator = '='): self
    {
        $condition = match ($operator) {
            '=' => ['term' => [$field => $value]],
            '!=' => ['bool' => ['must_not' => ['term' => [$field => $value]]]],
            default => throw new \InvalidArgumentException("Unsupported operator: $operator"),
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
        return $this->elasticsearch->search($this->query)->asArray();
    }

    public function getQuery(): array
    {
        return $this->query;
    }

    public function body(array $body): self
    {
        $this->query['body'] = array_merge_recursive($this->query['body'] ?? [], $body);
        return $this;
    }

    public function id(string $id): self
    {
        $this->query['id'] = $id;
        return $this;
    }

    public function indexExists(): bool
    {
        return $this->elasticsearch->indices()->exists([
            'index' => $this->query['index'] ?? null,
        ])->asBool();
    }

    public function delete(): array
    {
        return $this->elasticsearch->delete($this->query)->asArray();
    }
}
