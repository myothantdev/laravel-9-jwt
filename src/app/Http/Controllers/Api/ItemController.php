<?php

namespace App\Http\Controllers\Api;

use App\Models\Item;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\ItemCollection;

class ItemController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @param $uuid
     * @return ItemCollection|JsonResponse
     */
    public function index(Request $request, $uuid)
    {
        $category = Category::where('uuid', $uuid)->first();

        if (!$category) {
            return $this->msgResponse("You request ID:$uuid not found.", 404);
        }

        return new ItemCollection(Item::where('category_id', $category->id)->paginate(10));
    }
}
