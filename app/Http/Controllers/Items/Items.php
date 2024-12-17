<?php

namespace App\Http\Controllers\Items;

use App\Http\Controllers\Controller;
use App\Models\Items as mItems;
use App\goHoltz\API\Response as API;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class Items extends Controller
{
    /**
     * @var array<string, mixed>
     */
    protected array $fields = [];

    /**
     * @var int $pageItems
     */
    protected int $pageItems = 40;

    /**
     * Constructor
     * 
     * @param Request $request
     */
    public function __construct(protected Request $request)
    {
        $this->fields = $this->request->all();
        $this->pageItems = config('api.items_per_page', 40);
    }

    /**
     * List items
     * 
     * @return JsonResponse
     */
    public function listItems(): JsonResponse
    {
        if (($params = API::validate([
            'offset' => ['nullable', 'integer', 'min:0'],
            'limit' => ['nullable', 'integer', 'max:' . $this->pageItems * 2],
            'order' => ['nullable', 'string', 'in:asc,desc'],
            'sort' => ['nullable', 'string', 'in:name,price,created_at'],
            'filter' => ['nullable', 'string', 'in:name,description,price'],
            'filter_value' => ['nullable', 'string', 'min:1', 'max:255'],
            'filter_literally' => ['nullable', 'in:true,false,1,0,on,off'],
        ], $this->fields, $response, [
            'offset' => 0,
            'limit' => 30,
            'order' => 'asc',
            'sort' => 'created_at',
            'filter' => null,
            'filter_value' => null,
            'filter_literally' => false
        ])) instanceof JsonResponse) {
            return $params;
        }

        $params['filter_literally'] = in_array($params['filter_literally'], ['true', '1', 'on'], true);


        $items = mItems::orderBy($params['sort'], $params['order'])
            ->when($params['filter'] !== null, function ($query) use ($params) {
                return $query->where($params['filter'], $params['filter_literally'] ? $params['filter_value'] : 'like', $params['filter_literally'] ? null : "%{$params['filter_value']}%");
            });

        $total = $items->count();

        $items = $items->offset($params['offset'])
            ->limit($params['limit'])
            ->get();

        $response->setDataInfo([
            'count' => $items->count(),
            'total' => $total,
        ]);
        return API::success($response, null);
    }

    /**
     * Create a new item
     * 
     * @return JsonResponse
     */
    public function createItem(): JsonResponse
    {
        if (($params = API::validate([
            'name' => ['required', 'string', 'min:3', 'max:255'],
            'description' => ['required', 'string', 'min:3', 'max:255'],
            'price' => ['required', 'numeric', 'min:0'],
        ], $this->fields, $response)) instanceof JsonResponse) {
            return $params;
        }

        $item = mItems::create([
            'name' => $params['name'],
            'description' => $params['description'],
            'price' => $params['price'],
        ]);

        return API::success($response, [
            'id' => $item->id,
        ]);
    }

    /**
     * Update item
     * 
     * @return JsonResponse
     */
    public function updateItem(): JsonResponse
    {
        if (($params = API::validate([
            'id' => ['required', 'string', 'min:36', 'max:36'],
            'name' => ['required', 'string', 'min:3', 'max:255'],
            'description' => ['required', 'string', 'min:3', 'max:255'],
            'price' => ['required', 'numeric', 'min:0'],
        ], $this->fields, $response)) instanceof JsonResponse) {
            return $params;
        }

        $item = mItems::find($params['id']);
        if ($item === null) {
            return API::fail($response, "Item not found.");
        }

        $item->update([
            'name' => $params['name'],
            'description' => $params['description'],
            'price' => $params['price'],
        ]);

        return API::success($response, [
            'id' => $item->id,
        ]);
    }

    /**
     * Delete item
     * 
     * @return JsonResponse
     */
    public function deleteItem(): JsonResponse
    {
        if (($params = API::validate([
            'id' => ['required', 'string', 'min:36', 'max:36'],
        ], $this->fields, $response)) instanceof JsonResponse) {
            return $params;
        }

        $item = mItems::find($params['id']);
        if ($item === null) {
            return API::fail($response, "Item not found.");
        }

        return $item->delete() ? API::success($response, null) : API::fail($response, "Item not deleted.");
    }
}
