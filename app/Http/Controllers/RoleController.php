<?php

namespace App\Http\Controllers;

use App\Data\RoleData;
use App\Http\Requests\IndexRequest;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(IndexRequest $request)
    {
        $roles = Role::where(function (Builder $query) use ($request) {
            $query->where('name', 'like', '%'.$request->search.'%');
        })->get();

        return $this->responseSuccess([
            'results' => RoleData::collect($roles),
        ]);
    }
}
