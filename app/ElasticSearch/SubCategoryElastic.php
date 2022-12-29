<?php

namespace App\ElasticSearch;

use App\ElasticSearch\ElasticInterface;
use App\SubCategory;
use Illuminate\Http\Request;

class SubCategoryElastic extends ElasticDB implements ElasticInterface
{
    protected $tableName = 'subcategory';

    /**
     * @param \Illuminate\Http\Request $request
     * @return mixed
     */
    public function getAll(Request $request, int $perPage = 20)
    {
        $page = $request->get('page') ?? 1;
        $offset = $perPage * ($page - 1);
        $body = [
            'sort' => [
                [
                    'name.keyword' => [
                        'order' => 'asc'
                    ]
                ]
            ],
            'from' => $offset,
            'size' => $perPage,
            'query' => [
                'bool' => [
                    'filter' => [
                        [
                            'match_phrase' => [
                                'table_name' => $this->tableName
                            ]
                        ]
                    ]
                ]
            ]
        ];

        if ($request->get('q')) {
            $body['query']['bool']['filter'][] = [
                'match_phrase_prefix' => [
                    'name' => $request->get('q')
                ]
            ];
        }

        if ($request->get('slug')) {
            $body['query']['bool']['filter'][] = [
                'match_phrase' => [
                    'slug' => $request->get('slug')
                ]
            ];
        }

        if ($request->get('category_slug')) {
            $body['query']['bool']['filter'][] = [
                'match_phrase' => [
                    'category.slug' => $request->get('category_slug')
                ]
            ];
        }

        if ($request->get('category_id')) {
            $body['query']['bool']['filter'][] = [
                'term' => [
                    'category.id' => $request->get('category_id')
                ]
            ];
        }

        $params = [
            'index' => env('ELASTICSEARCH_INDEX', 'laravel_news'),
            'type'  => env('ELASTICSEARCH_TYPE', '_doc'),
            'body'  => $body
        ];
        return $this->connection->search($params);
    }

    /**
     *
     * @param int $id
     * @return mixed
     */
    public function getById(int $id)
    {
        $params = [
            'index' => env('ELASTICSEARCH_INDEX', 'laravel_news'),
            'type'  => env('ELASTICSEARCH_TYPE', '_doc'),
            'body'  => [
                'query' => [
                    'bool' => [
                        'filter' => [
                            'match_phrase' => [
                                'indexing_id' => $this->tableName . ":" . $id
                            ]
                        ]
                    ]
                ]
            ]
        ];
        $response = $this->connection->search($params);

        return $response;
    }

    /**
     *
     * @param int $id
     * @return mixed
     */
    public function insert(int $id)
    {
        $getSubCategory = SubCategory::whereId($id)->with(['category'])->get();

        $body = $getSubCategory->toArray()[0];

        $body['table_name']  = $this->tableName;
        $body['indexing_id'] = $this->tableName . ':' . $body['id'];
        $params = [
            'index' => env('ELASTICSEARCH_INDEX', 'laravel_news'),
            'type'  => env('ELASTICSEARCH_TYPE', '_doc'),
            'id'    => $body['indexing_id'],
            'body'  => $body
        ];
        return $this->connection->index($params);
    }

    /**
     *
     * @param int $id
     * @return mixed
     */
    public function update(int $id)
    {
        $getSubCategory = SubCategory::whereId($id)->with(['category'])->get();

        $body = $getSubCategory->toArray()[0];
        $body['table_name']  = $this->tableName;
        $body['indexing_id'] = $this->tableName . ':' . $body['id'];

        $params = [
            'index' => env('ELASTICSEARCH_INDEX', 'laravel_news'),
            'type'  => env('ELASTICSEARCH_TYPE', '_doc'),
            'id'    => $body['indexing_id'],
            'body'  => [
                'doc' => $body
            ]
        ];
        return $this->connection->update($params);
    }

    /**
     *
     * @param int $id
     * @return mixed
     */
    public function delete(int $id)
    {
        $params = [
            'index' => env('ELASTICSEARCH_INDEX', 'laravel_news'),
            'type'  => env('ELASTICSEARCH_TYPE', '_doc'),
            'id'    => $this->tableName . ':' . $id,
        ];
        return $this->connection->delete($params);
    }
}
