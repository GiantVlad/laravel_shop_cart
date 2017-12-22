<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;


class AdminUsersController extends Controller
{
    private $user;
    public function __construct (User $user)
    {
        $this->middleware('auth:admin');
        $this->user = $user;
    }

    public function list ()
    {
        $users = $this->user->paginate(15);
        return view('admin.users', ['users' => $users]);
    }

    public function delete (Request $request)
    {

    }

    public function search(Request $request)
    {
        $keyword = $request->keyword;

        if ($keyword!='') {
            $users = $this->user->where("name", "LIKE","%$keyword%")
                ->orWhere("email", "LIKE", "%$keyword%")
                ->orWhere("id", "=", "$keyword")
                ->paginate(15);
            return view('admin.users', ['users' => $users]);
        }

        return back();
    }


    public function showEditForm (int $id)
    {
        if ($id) $user = $this->user->find($id);

        return view('admin.edit-user', ['user' => $user]);

    }

    public function update (Request $request)
    {
        $this->validate($request, [
            'name' => 'required | min:3 | max:150',
            'email' => 'required | email',
            'id' => 'required'
        ]);

        $updated = $this->user->where('id', $request->id)
            ->update(['name' => $request->name, 'email' => $request->email]);

        if ($updated) {
            $message = 'User ' . $request->name . ' was changed!';
            return back()->with('message', $message);
        } else {
            return back()->withErrors('User not found');
        }


    }

    public function deleteCart (Request $request)
    {
        $this->validate($request, [
            'id' => 'required'
        ]);

        $updated = $this->user->markForLogoutById($request->id);

        if ($updated) {
            $message = 'Users Cart was cleared!';
            return back()->with('message', $message);
        } else {
            return back()->withErrors('User not found');
        }
    }


}
