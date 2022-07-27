<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Warehouse;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Category::all();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required'
        ]);
        return Category::create($request->all());
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function show(Category $category)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function edit(Category $category)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $category = Category::findOrFail($id);
        return $category->update($request->all());
    }

    public function destroy($id)
    {
        $category = Category::findOrFail($id);
        $warehouses = Warehouse::all();
        foreach ($warehouses as $warehouse) {
            if ($warehouse->storage_bins) {
                $new_bins = (array)$warehouse->storage_bins;
                foreach ((array)$warehouse->storage_bins as $bin) {

                    $temp = array_column($new_bins, 'category_id');
                    $found_key = array_search($id, $temp);
                    // return var_dump($new_bins[$found_key]);
                    $new_bins[$found_key]["category_id"] = null;
                    if ($new_bins[$found_key]["inventory_id"] != -1 && $new_bins[$found_key]["inventory_id"] != null) {
                        $new_bins[$found_key]["inventory_id"] = -1;
                    }
                }
                $warehouse->storage_bins = $new_bins;
                $warehouse->save();
            }
        }
        if ($category->inventories) {
            foreach ($category->inventories as $inventory) {
                $inventory->update(["category_id" => null]);
            }
        }
        return Category::destroy($id);
    }
}
