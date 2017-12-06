<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Catalog;

class AdminCategoriesController extends Controller
{
    public function __construct ()
    {
        $this->middleware('auth:admin');
    }

    public function list()
    {
        $categories = Catalog::leftJoin('catalogs as cat_parent', 'cat_parent.id' , '=', 'catalogs.parent_id')
            ->get(['catalogs.*', 'cat_parent.name as parent_name']);
        return view('admin.categories', ['categories' => $categories] );
    }

    public function delete($id, Request $request)
    {

        if (!$request->id || ($request->id != $id)) return back()->withErrors('Server Error... Please try again.');; //ToDo create error

        $category = Catalog::find($request->id);

        if (!$category) return back()->withErrors('Server Error... Category not found');

        if ($category->products->first()) return back()->withErrors('Canceled. Category '.$category->name.' has a children Products!');

        $children = Catalog::where('parent_id', $category->id)->first();
        if ($children) return back()->withErrors('Canceled. Category '.$category->name.' has a children Categories!');

        $category->delete();
        return redirect()->back()->with('message', 'Category '.$category->name.' was deleted!');
    }

    public function showEditForm($id=null)
    {
        $categories = Catalog::all('id', 'name');
        return view('admin.edit-category', ['categories_names' => $categories]);
    }

    public function update(Request $request)
    {
        //ToDO check return if not valid
        $this->validate($request, [
            'name' => 'required | min:3 | max:30',
            'priority' => 'max:99'
        ]);

        $category = new Catalog;
        $category->name = $request->name;
        $category->priority = $request->priority;
        $category->description = $request->description;
        if ($request->parent) $category->parent_id = $request->parent;
        //ToDO image upload
        //$category->parent = $request->image;
        $category->save();

        //$categories = Catalog::all('name');
        return redirect(url('admin/categories'))->with('message', 'Catalog was added!');
    }
}
