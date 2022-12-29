<?php

namespace App\Repositories;

use App\ElasticSearch\TagElastic;
use App\Tag;
use App\Http\Controllers\Api\ApiResponse;
use App\Http\Requests\TagRequest;
use App\Http\Resources\TagResource;
use App\Interfaces\TagInterface;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class TagRepository implements TagInterface
{

    public function getAllTag(Request $request)
    {
        $perPage = $request->limit ?? 20;
        $perPage = $perPage > 50 ? 20 : $perPage;

        try {
            $keyRedis = 'tag:collection:' . $perPage . ':' . json_encode($request->all());
            if (Cache::has($keyRedis)) {
                $respond = json_decode(Cache::get($keyRedis), true);
                if (!empty($respond)) {
                    return response()->json($respond);
                }
            }

            $tagElastic = new TagElastic();
            $getData    = $tagElastic->getAll($request, $perPage)['hits'];
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

    public function getTagById($id)
    {
        try {
            $keyRedis = 'tag:item:' . $id;
            if (Cache::has($keyRedis)) {
                $respond = json_decode(Cache::get($keyRedis));
                if (!empty($respond)) {
                    return ApiResponse::successItem($respond);
                }
            }

            $tagElastic = new TagElastic();
            $getData     = $tagElastic->getById($id)['hits'];
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

    public function insertTag(TagRequest $request)
    {
        $name = $request->name;
        $slug = Str::slug($name);
        $tag = Tag::where('slug', $slug)->count();
        if ($tag) {
            return ApiResponse::badRequest('Duplicate Tag');
        }

        $tag = new Tag();
        $tag->name = $name;
        $tag->slug = $slug;
        $tag->save();

        $tagElastic = new TagElastic();
        $tagElastic->insert($tag->id);

        $respond = new TagResource($tag);
        return response()->json($respond, Response::HTTP_OK);
    }

    public function updateTag(TagRequest $request, int $id)
    {
        try {
            $tag = Tag::findOrFail($id);
            $name           = $request->name;
            $slug           = Str::slug($name);
            $tag->name = $name;
            $tag->slug = $slug;
            $tag->save();

            $tagElastic = new TagElastic();
            $tagElastic->update($tag->id);

            Cache::forget('tag:item:' . $id);

            $respond = new TagResource($tag);
            return response()->json($respond, Response::HTTP_OK);
        } catch (\Throwable $th) {
            return ApiResponse::badRequest('Tag Not Found');
        }
    }

    public function deleteTag(int $id)
    {
        try {
            Tag::destroy($id);

            $tagElastic = new TagElastic();
            $tagElastic->delete($id);

            Cache::forget('tag:item:' . $id);

            return ApiResponse::successDelete();
        } catch (\Throwable $th) {
            return ApiResponse::badRequest('Tag Not Found');
        }
    }
}
