<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class AdminUsersController extends Controller
{
    private User $user;
    
    /**
     * AdminUsersController constructor.
     * @param User $user
     */
    public function __construct(User $user)
    {
        $this->middleware('auth:admin');
        $this->user = $user;
    }
    
    /**
     * @return View
     */
    public function list(): View
    {
        $users = $this->user->paginate(15);
        
        return view('admin.users', ['users' => $users]);
    }
    
    /**
     * @param Request $request
     */
    public function delete(Request $request): void
    {
    }
    
    /**
     * @param Request $request
     * @return View|RedirectResponse
     */
    public function search(Request $request): View|RedirectResponse
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
    
    /**
     * @param int $id
     * @return View|RedirectResponse
     */
    public function showEditForm(int $id): View|RedirectResponse
    {
        if ($id) {
            $user = $this->user->findOrFail($id);
        } else {
            return back()->withErrors('Invalid ID');
        }
        if (!$user instanceof User) {
            return back()->withErrors('User not found');
        }

        return view('admin.edit-user', ['user' => $user]);
    }
    
    /**
     * @param Request $request
     * @return RedirectResponse
     * @throws ValidationException
     */
    public function update(Request $request): RedirectResponse
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
    
    /**
     * @param Request $request
     * @return RedirectResponse
     * @throws ValidationException
     */
    public function deleteCart(Request $request): RedirectResponse
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
