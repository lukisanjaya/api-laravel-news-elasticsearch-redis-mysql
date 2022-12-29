<?php

namespace App\Interfaces;

use Illuminate\Http\Request;
use App\Http\Requests\TagRequest;

interface TagInterface
{
    /**
     * Get all tag
     *
     * @method GET api/tag
     * @param \Illuminate\Http\Request  $request
     * @access public
     */
    public function getAllTag(Request $request);

    /*
     * Get tag by id
     *
     * @method GET api/tag/{id}
     * @access public
     */
    public function getTagById($id);


    /*
     * Insert tag
     *
     * @method  POST api/tag
     * @param \Illuminate\Http\Request  $request
     * @access public
     */
    public function insertTag(TagRequest $request);

    /*
     * Update tag
     *
     * @method PUT api/tag/{id}
     * @param \Illuminate\Http\Request  $request
     * @param int $id
     * @access public
     */
    public function updateTag(TagRequest $request, int $id);

    /*
     * Delete Tag
     *
     * @method Delete api/tag
     * @param int $id
     * @access public
     */
    public function deleteTag(int $id);
}
