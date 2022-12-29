<?php

namespace App\Repositories;

use App\ElasticSearch\CategoryElastic;
use App\Category;
use App\Http\Controllers\Api\ApiResponse;
use App\Http\Requests\CategoryRequest;
use App\Http\Resources\CategoryResource;
use App\Interfaces\CategoryInterface;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class CategoryRepository implements CategoryInterface
{

    public function getAllCategory(Request $request)
    {
        $perPage = $request->limit ?? 20;
        $perPage = $perPage > 50 ? 20 : $perPage;

        try {
            $keyRedis = 'category:collection:' . $perPage . ':' . json_encode($request->all());
            if (Cache::has($keyRedis)) {
                $respond = json_decode(Cache::get($keyRedis), true);
                if (!empty($respond)) {
                    return response()->json($respond);
                }
            }

            $categoryElastic = new CategoryElastic();
            $getData    = $categoryElastic->getAll($request, $perPage)['hits'];
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

    public function getCategoryById($id)
    {
        try {
            $keyRedis = 'category:item:' . $id;
            if (Cache::has($keyRedis)) {
                $respond = json_decode(Cache::get($keyRedis));
                if (!empty($respond)) {
                    return ApiResponse::successItem($respond);
                }
            }

            $categoryElastic = new CategoryElastic();
            $getData     = $categoryElastic->getById($id)['hits'];
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

    public function insertCategory(CategoryRequest $request)
    {
        $name = $request->name;
        $slug = Str::slug($name);
        $category = Category::where('slug', $slug)->count();
        if ($category) {
            return ApiResponse::badRequest('Duplicate Category');
        }

        $category = new Category();
        $category->name = $name;
        $category->slug = $slug;
        $category->save();

        $categoryElastic = new CategoryElastic();
        $categoryElastic->insert($category->id);

        $respond = new CategoryResource($category);
        return response()->json($respond, Response::HTTP_OK);
    }

    public function updateCategory(CategoryRequest $request, int $id)
    {
        try {
            $category = Category::findOrFail($id);
            $name           = $request->name;
            $slug           = Str::slug($name);
            $category->name = $name;
            $category->slug = $slug;
            $category->save();

            $categoryElastic = new CategoryElastic();
            $categoryElastic->update($category->id);

            Cache::forget('category:item:' . $id);

            $respond = new CategoryResource($category);
            return response()->json($respond, Response::HTTP_OK);
        } catch (\Throwable $th) {
            return ApiResponse::badRequest('Category Not Found');
        }
    }

    public function deleteCategory(int $id)
    {
        try {
            Category::destroy($id);

            $categoryElastic = new CategoryElastic();
            $categoryElastic->delete($id);

            Cache::forget('category:item:' . $id);

            return ApiResponse::successDelete();
        } catch (\Throwable $th) {
            return ApiResponse::badRequest('Category Not Found');
        }
    }
}
