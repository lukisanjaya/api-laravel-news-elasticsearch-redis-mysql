<?php

namespace App\Interfaces;

use Illuminate\Http\Request;
use App\Http\Requests\SubCategoryRequest;

interface SubCategoryInterface
{
    /**
     * Get all subcategory
     *
     * @method GET api/subcategory
     * @param \Illuminate\Http\Request  $request
     * @access public
     */
    public function getAllSubCategory(Request $request);

    /*
     * Get subcategory by id
     *
     * @method GET api/subcategory/{id}
     * @access public
     */
    public function getSubCategoryById($id);


    /*
     * Insert subcategory
     *
     * @method  POST api/subcategory
     * @param \Illuminate\Http\Request  $request
     * @access public
     */
    public function insertSubCategory(SubCategoryRequest $request);

    /*
     * Update subcategory
     *
     * @method PUT api/subcategory/{id}
     * @param \Illuminate\Http\Request  $request
     * @param int $id
     * @access public
     */
    public function updateSubCategory(SubCategoryRequest $request, int $id);

    /*
     * Delete SubCategory
     *
     * @method Delete api/subcategory
     * @param int $id
     * @access public
     */
    public function deleteSubCategory(int $id);
}
