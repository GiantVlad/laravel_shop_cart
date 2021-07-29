<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Catalog;
use Illuminate\Routing\Redirector;
use Illuminate\Validation\ValidationException;

class AdminCategoriesController extends Controller
{
    public function __construct ()
    {
        $this->middleware('auth:admin');
    }
    
    /**
     * @return View
     */
    public function list()
    {
        $categories = Catalog::leftJoin('catalogs as cat_parent', 'cat_parent.id' , '=', 'catalogs.parent_id')
            ->get(['catalogs.*', 'cat_parent.name as parent_name']);
        
        return view('admin.categories', ['categories' => $categories]);
    }
    
    /**
     * @param int $id
     * @param Request $request
     * @return RedirectResponse
     */
    public function delete(int $id, Request $request): RedirectResponse
    {
        if (!$request->id || ($request->id != $id)) {
            return back()->withErrors('Server Error... Please try again.'); //ToDo create error
        }

        $category = Catalog::find($request->id);

        if (!$category) {
            return back()->withErrors('Server Error... Category not found');
        }

        if ($category->products()->first()) {
            return back()->withErrors('Canceled. Category '.$category->name.' has a children Products!');
        }

        $children = Catalog::where('parent_id', $category->id)->first();
        if ($children) {
            return back()->withErrors('Canceled. Category '.$category->name.' has a children Categories!');
        }

        $category->delete();
        
        return redirect()->back()->with('message', 'Category '.$category->name.' was deleted!');
    }
    
    /**
     * @param int|null $id
     * @return View
     */
    public function showEditForm(int $id=null): View
    {
        $parentCategories = Catalog::all('id', 'name');
        $category = null;
        if ($id) {
            $category = Catalog::findOrFail($id);
        }

        return view(
            'admin.edit-category',
            ['category'=> $category, 'parent_categories_names' => $parentCategories]
        );
    }
    
    /**
     * @param Request $request
     * @return RedirectResponse|Redirector
     * @throws ValidationException
     */
    public function update(Request $request): RedirectResponse|Redirector
    {
        $this->validate($request, [
            'name' => 'required | min:3 | max:30',
            'priority' => 'max:99',
            'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048'
        ]);
        $id = (int)$request->get('id');
        if ($id) {
            $category = Catalog::findOrFail($id);
            $message = 'Category '. $request->get('name') .' was changed!';
        } else {
            $category = new Catalog;
            $message = 'Category '. $request->get('name') .' was added!';
        }

        $category->name = $request->get('name');
        $category->priority = $request->get('priority');
        $category->description = $request->get('description');
        if ($request->get('parent')) {
            $category->parent_id = $request->get('parent');
        }

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
