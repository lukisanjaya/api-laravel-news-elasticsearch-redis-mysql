<?php

namespace App\Repositories;

use App\ElasticSearch\SubCategoryElastic;
use App\SubCategory;
use App\Http\Controllers\Api\ApiResponse;
use App\Http\Requests\SubCategoryRequest;
use App\Http\Resources\SubCategoryResource;
use App\Interfaces\SubCategoryInterface;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class SubCategoryRepository implements SubCategoryInterface
{
    public function getAllSubCategory(Request $request)
    {
        $perPage = $request->limit ?? 20;
        $perPage = $perPage > 50 ? 20 : $perPage;

        try {
            $keyRedis = 'subcategory:collection:' . $perPage . ':' . json_encode($request->all());
            if (Cache::has($keyRedis)) {
                $respond = json_decode(Cache::get($keyRedis), true);
                if (!empty($respond)) {
                    return response()->json($respond);
                }
            }

            $subCategoryElastic = new SubCategoryElastic();
            $getData    = $subCategoryElastic->getAll($request, $perPage)['hits'];
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

    public function getSubCategoryById($id)
    {
        try {
            $keyRedis = 'subcategory:item:' . $id;
            if (Cache::has($keyRedis)) {
                $respond = json_decode(Cache::get($keyRedis));
                if (!empty($respond)) {
                    return ApiResponse::successItem($respond);
                }
            }

            $subCategoryElastic = new SubCategoryElastic();
            $getData     = $subCategoryElastic->getById($id)['hits'];
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

    public function insertSubCategory(SubCategoryRequest $request)
    {
        $name = $request->name;
        $slug = Str::slug($name);
        $subCategory = SubCategory::where('slug', $slug)->count();
        if ($subCategory) {
            return ApiResponse::badRequest('Duplicate Category');
        }

        $subCategory              = new SubCategory();
        $subCategory->name        = $name;
        $subCategory->category_id = $request->category_id;
        $subCategory->slug        = $slug;
        $subCategory->save();

        $categoryElastic = new SubCategoryElastic();
        $categoryElastic->insert($subCategory->id);

        $respond = new SubCategoryResource($subCategory);
        return response()->json($respond, Response::HTTP_OK);
    }

    public function updateSubCategory(SubCategoryRequest $request, int $id)
    {
        try {
            $subCategory              = SubCategory::findOrFail($id);
            $name                     = $request->name;
            $slug                     = Str::slug($name);
            $subCategory->name        = $name;
            $subCategory->category_id = $request->category_id;
            $subCategory->slug        = $slug;
            $subCategory->save();

            $categoryElastic = new SubCategoryElastic();
            $categoryElastic->update($subCategory->id);

            Cache::forget('subcategory:item:' . $id);

            $respond = new SubCategoryResource($subCategory);
            return response()->json($respond, Response::HTTP_OK);
        } catch (\Throwable $th) {
            return ApiResponse::badRequest('Category Not Found');
        }
    }

    public function deleteSubCategory(int $id)
    {
        try {
            SubCategory::destroy($id);

            $categoryElastic = new SubCategoryElastic();
            $categoryElastic->delete($id);

            Cache::forget('subcategory:item:' . $id);

            return ApiResponse::successDelete();
        } catch (\Throwable $th) {
            return ApiResponse::badRequest('Category Not Found');
        }
    }
}
