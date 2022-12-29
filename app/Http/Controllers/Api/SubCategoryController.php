<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\SubCategoryRequest;
use App\Interfaces\SubCategoryInterface;
use Illuminate\Http\Request;

class SubCategoryController extends Controller
{
    protected $subCategoryInterface;

    public function __construct(SubCategoryInterface $subCategoryInterface)
    {
        $this->subCategoryInterface = $subCategoryInterface;
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return $this->subCategoryInterface->getAllSubCategory($request);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(SubCategoryRequest $request)
    {
        return $this->subCategoryInterface->insertSubCategory($request);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return $this->subCategoryInterface->getSubCategoryById($id);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(SubCategoryRequest $request, $id)
    {
        return $this->subCategoryInterface->updateSubCategory($request, $id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        return $this->subCategoryInterface->deleteSubCategory($id);
    }
}
