<?php

use Illuminate\Foundation\Inspiring;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your Closure based console
| commands. Each Closure is bound to a command instance allowing a
| simple approach to interacting with each command's IO methods.
|
*/

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->describe('Display an inspiring quote');

Artisan::command('elasticsearch', function () {
    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL            => 'http://localhost:9200/laravel_news',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING       => '',
        CURLOPT_MAXREDIRS      => 10,
        CURLOPT_TIMEOUT        => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST  => 'PUT',
        CURLOPT_POSTFIELDS     => '{
  "settings": {
  "index": {
    "analysis": {
      "filter": {
        "email": {
          "type": "pattern_capture",
          "preserve_original": true,
          "patterns": [
            "([^@]+)",
            "(\\\\p{L}+)",
            "(\\\\d+)",
            "@(.+)",
            "([^-@]+)"
          ]
        }
      },
      "analyzer": {
        "email": {
          "tokenizer": "uax_url_email",
          "filter": [
            "email",
            "lowercase",
            "unique"
          ]
        }
      }
    }
  }
},
  "mappings": {
    "_doc": {
      "properties": {
        "table_name": {
          "type": "keyword"
        },
        "indexing_id": {
          "type": "keyword"
        },
        "id": {
          "type": "integer"
        },
        "author_id": {
          "type": "integer"
        },
        "category_id": {
          "type": "integer"
        },
        "subcategory_id": {
          "type": "integer"
        },
        "title": {
          "type": "text",
          "fields": {
            "keyword": {
              "type": "keyword"
            }
          }
        },
        "slug": {
          "type": "keyword"
        },
        "teaser": {
          "type": "text"
        },
        "category": {
          "properties": {
            "id": {
              "type": "integer"
            },
            "title": {
              "type": "text",
              "fields": {
                "keyword": {
                  "type": "keyword"
                }
              }
            },
            "slug": {
              "type": "keyword"
            }
          }
        },
        "subcategory": {
          "properties": {
            "id": {
              "type": "integer"
            },
            "title": {
              "type": "text",
              "fields": {
                "keyword": {
                  "type": "keyword"
                }
              }
            },
            "slug": {
              "type": "keyword"
            }
          }
        },
        "content": {
          "type": "text"
        },
        "image": {
          "type": "text"
        },
        "image_caption": {
          "type": "text"
        },
        "tag": {
          "properties": {
            "id": {
              "type": "integer"
            },
            "title": {
              "type": "text",
              "fields": {
                "keyword": {
                  "type": "keyword"
                }
              }
            },
            "slug": {
              "type": "keyword"
            }
          }
        },
        "user": {
          "properties": {
            "id": {
              "type": "integer"
            },
            "name": {
              "type": "text",
              "fields": {
                "keyword": {
                  "type": "keyword"
                }
              }
            },
            "username": {
              "type": "keyword"
            },
            "avatar": {
              "type": "text"
            }
          }
        },
        "deleted_at": {
          "type": "date",
          "format": "yyyy-MM-dd HH:mm:ss"
        },
        "published_at": {
          "type": "date",
          "format": "yyyy-MM-dd HH:mm:ss"
        },
        "created_at": {
          "type": "date",
          "format": "yyyy-MM-dd HH:mm:ss"
        },
        "updated_at": {
          "type": "date",
          "format": "yyyy-MM-dd HH:mm:ss"
        },
        "email": {
          "type": "text",
          "analyzer": "email"
        },
        "username": {
          "type": "keyword"
        },
        "avatar": {
          "type": "text"
        },
        "address": {
          "type": "text"
        },
        "roles": {
          "type": "keyword"
        },
        "email_verified_at": {
          "type": "date",
          "format": "yyyy-MM-dd HH:mm:ss"
        }
      }
    }
  }
}',
        CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json'
        ),
    ));

    $response = curl_exec($curl);

    curl_close($curl);
    echo $response;
})->describe('Mapping And Settings Elasticsearch');
