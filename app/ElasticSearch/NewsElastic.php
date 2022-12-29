<?php

namespace App\ElasticSearch;

use App\ElasticSearch\ElasticInterface;
use App\News;
use Illuminate\Http\Request;

class NewsElastic extends ElasticDB implements ElasticInterface
{
    protected $tableName = 'news';

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
                    'published_at' => [
                        'order' => 'desc'
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
                    'title' => $request->get('q')
                ]
            ];
        }

        if ($request->get('user_id')) {
            $body['query']['bool']['filter'][] = [
                'term' => [
                    'user.id' => $request->get('user_id')
                ]
            ];
        }

        if ($request->get('username')) {
            $body['query']['bool']['filter'][] = [
                'term' => [
                    'user.username' => $request->get('username')
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

        if ($request->get('category_id')) {
            $body['query']['bool']['filter'][] = [
                'term' => [
                    'category.id' => $request->get('category_id')
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

        if ($request->get('tag_id')) {
            $body['query']['bool']['filter'][] = [
                'term' => [
                    'tag.id' => $request->get('tag_id')
                ]
            ];
        }

        if ($request->get('tag_slug')) {
            $body['query']['bool']['filter'][] = [
                'match_phrase' => [
                    'tag.slug' => $request->get('tag_slug')
                ]
            ];
        }

        if ($request->get('subcategory_slug')) {
            $body['query']['bool']['filter'][] = [
                'match_phrase' => [
                    'subcategory.slug' => $request->get('subcategory_slug')
                ]
            ];
        }

        if ($request->get('subcategory_id')) {
            $body['query']['bool']['filter'][] = [
                'term' => [
                    'subcategory.id' => $request->get('subcategory_id')
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
        $getNews = News::whereId($id)->with(
            [
                'category' => function ($query) {
                    $query->select(['id', 'name', 'slug']);
                },
                'subcategory' => function ($query) {
                    $query->select(['id', 'name', 'slug']);
                },
                'tag' => function ($query) {
                    $query->select(['tags.id', 'tags.name', 'tags.slug']);
                },
                'user' => function ($query) {
                    $query->select(['id', 'name', 'username', 'avatar']);
                }
            ]
        )->get();

        $body = $getNews->toArray()[0];

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
        $getNews = News::whereId($id)->with(
            [
                'category' => function ($query) {
                    $query->select(['id', 'name', 'slug']);
                },
                'subcategory' => function ($query) {
                    $query->select(['id', 'name', 'slug']);
                },
                'tag' => function ($query) {
                    $query->select(['tags.id', 'tags.name', 'tags.slug']);
                },
                'user' => function ($query) {
                    $query->select(['id', 'name', 'username', 'avatar']);
                }
            ]
        )->get();

        $body = $getNews->toArray()[0];

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
