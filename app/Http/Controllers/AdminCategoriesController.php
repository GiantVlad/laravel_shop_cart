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

        if ($category->products()->first()) return back()->withErrors('Canceled. Category '.$category->name.' has a children Products!');

        $children = Catalog::where('parent_id', $category->id)->first();
        if ($children) return back()->withErrors('Canceled. Category '.$category->name.' has a children Categories!');

        $category->delete();
        return redirect()->back()->with('message', 'Category '.$category->name.' was deleted!');
    }

    public function showEditForm($id=null)
    {
        $parentCategories = Catalog::all('id', 'name');
        $category = null;
        if ($id) $category = Catalog::find($id);

        return view('admin.edit-category', ['category'=> $category, 'parent_categories_names' => $parentCategories]);
    }

    public function update(Request $request)
    {
        $this->validate($request, [
            'name' => 'required | min:3 | max:30',
            'priority' => 'max:99',
            'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048'
        ]);
        if ($request->id) {
            $category = Catalog::find($request->id);
            $message = 'Category '. $request->name .' was changed!';
        } else {
            $category = new Catalog;
            $message = 'Category '. $request->name .' was added!';
        }

        $category->name = $request->name;
        $category->priority = $request->priority;
        $category->description = $request->description;
        if ($request->parent) $category->parent_id = $request->parent;

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = 'cat_'.time().'.'.$image->getClientOriginalExtension();
            $imageDestinationPath = public_path('images/categories/');
            $image->move($imageDestinationPath, $imageName);
            $category->image = 'images/categories/'.$imageName;
        }
        //todo image resize

        $category->save();

        return redirect(url('admin/categories'))->with('message', $message);
    }
}
