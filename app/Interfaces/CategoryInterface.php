<?php

namespace App\Interfaces;

use Illuminate\Http\Request;
use App\Http\Requests\CategoryRequest;

interface CategoryInterface
{
    /**
     * Get all category
     *
     * @method GET api/category
     * @param \Illuminate\Http\Request  $request
     * @access public
     */
    public function getAllCategory(Request $request);

    /*
     * Get category by id
     *
     * @method GET api/category/{id}
     * @access public
     */
    public function getCategoryById($id);


    /*
     * Insert category
     *
     * @method  POST api/category
     * @param \Illuminate\Http\Request  $request
     * @access public
     */
    public function insertCategory(CategoryRequest $request);

    /*
     * Update category
     *
     * @method PUT api/category/{id}
     * @param \Illuminate\Http\Request  $request
     * @param int $id
     * @access public
     */
    public function updateCategory(CategoryRequest $request, int $id);

    /*
     * Delete Category
     *
     * @method Delete api/category
     * @param int $id
     * @access public
     */
    public function deleteCategory(int $id);
}
