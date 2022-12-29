<?php

namespace App\ElasticSearch;

use Illuminate\Http\Request;

interface ElasticInterface
{
    function getAll(Request $request);
    function getById(int $id);
    function insert(int $id);
    function update(int $id);
    function delete(int $id);
}
