<?php

namespace App\Repositories;

use App\ElasticSearch\NewsElastic;
use App\Helpers\ImageHelper;
use App\News;
use App\Http\Controllers\Api\ApiResponse;
use App\Http\Requests\NewsRequest;
use App\Http\Resources\NewsResource;
use App\Interfaces\NewsInterface;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class NewsRepository implements NewsInterface
{

    public function getAllNews(Request $request)
    {
        $perPage = $request->limit ?? 20;
        $perPage = $perPage > 50 ? 20 : $perPage;

        try {
            $keyRedis = 'news:collection:' . $perPage . ':' . json_encode($request->all());
            if (Cache::has($keyRedis)) {
                $respond = json_decode(Cache::get($keyRedis), true);
                if (!empty($respond)) {
                    return response()->json($respond);
                }
            }

            $newsElastic = new NewsElastic();
            $getData    = $newsElastic->getAll($request, $perPage)['hits'];
            $total      = $getData['total'];
            if (!$total) {
                throw new \Exception("Data Not Found");
            }

            $return = ApiResponse::successElasticPaginate($request, $getData, $perPage);
            Cache::put($keyRedis, json_encode($return->original), now()->addMinutes(2));

            return $return;
        } catch (\Exception $e) {
            return ApiResponse::notFound();
        }

    }

    public function getNewsById($id)
    {
        try {
            $keyRedis = 'news:item:' . $id;
            if (Cache::has($keyRedis)) {
                $respond = json_decode(Cache::get($keyRedis));
                if (!empty($respond)) {
                    return ApiResponse::successItem($respond);
                }
            }

            $newsElastic = new NewsElastic();
            $getData     = $newsElastic->getById($id)['hits'];
            $total       = $getData['total'];
            $data        = $getData['hits'];
            if (!$total) {
                throw new \Exception("Data Not Found");
            }
            $respond = array_column($data, '_source')[0];

            Cache::put($keyRedis, json_encode($respond), now()->addMinutes(2));

            return ApiResponse::successItem($respond);
        } catch (\Exception $e) {
            return ApiResponse::notFound();
        }
    }

    public function insertNews(NewsRequest $request)
    {
        $title = $request->title;
        $slug = Str::slug($title);
        $news = News::where('slug', $slug)->count();
        if ($news) {
            return ApiResponse::badRequest('Duplicate News');
        }

        $news        = new News();
        $requestData = $request->all();

        $requestData['slug']      = $request->get('title');
        $requestData['author_id'] = Auth::guard()->user()->id;
        if ($request->file('image')) {
            $requestData['image'] = ImageHelper::uploadFile($request, $request->get('title'), 'articles');
        }

        $news->fill($requestData);
        $news->save();

        $news->tag()->attach(
            $request->get('tags')
        );

        $newsElastic = new NewsElastic();
        $newsElastic->insert($news->id);

        $respond = new NewsResource($news);

        return response()->json($respond, Response::HTTP_OK);
    }

    public function updateNews(Request $request, int $id)
    {
        try {
            $news        = News::findOrFail($id);
            $requestData = $request->all();
            $requestData['slug']      = $request->get('title');
            $requestData['author_id'] = Auth::guard()->user()->id;
            if ($request->file('image')) {
                $requestData['image'] = ImageHelper::uploadFile($request, $request->get('title'), 'articles');
            }

            $news->fill($requestData);
            $news->save();

            $newsElastic = new NewsElastic();
            $newsElastic->update($id);

            Cache::forget('news:item:' . $id);

            $respond = new NewsResource($news);
            return response()->json($respond, Response::HTTP_OK);
        } catch (\Throwable $th) {
            return ApiResponse::badRequest('News Not Found');
        }
    }

    public function deleteNews(int $id)
    {
        try {
            News::destroy($id);

            $newsElastic = new NewsElastic();
            $newsElastic->delete($id);

            Cache::forget('news:item:' . $id);

            return ApiResponse::successDelete();
        } catch (\Throwable $th) {
            return ApiResponse::badRequest('News Not Found');
        }
    }
}
