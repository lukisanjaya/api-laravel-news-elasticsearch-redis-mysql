<?php

namespace App\Interfaces;

use Illuminate\Http\Request;
use App\Http\Requests\NewsRequest;

interface NewsInterface
{
    /**
     * Get all news
     *
     * @method GET api/news
     * @param \Illuminate\Http\Request  $request
     * @access public
     */
    public function getAllNews(Request $request);

    /*
     * Get news by id
     *
     * @method GET api/news/{id}
     * @access public
     */
    public function getNewsById($id);

    /*
     * Insert news
     *
     * @method  POST api/news
     * @param \Illuminate\Http\Request  $request
     * @access public
     */
    public function insertNews(NewsRequest $request);

    /*
     * Update news
     *
     * @method PUT api/news/{id}
     * @param \Illuminate\Http\Request  $request
     * @param int $id
     * @access public
     */
    public function updateNews(Request $request, int $id);

    /*
     * Delete News
     *
     * @method Delete api/news
     * @param int $id
     * @access public
     */
    public function deleteNews(int $id);
}
