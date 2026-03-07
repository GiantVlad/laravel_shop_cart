<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

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
    public function list(): Response
    {
        $users = $this->user
            ->paginate(15)
            ->through(static fn (User $user): array => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ]);
        
        return Inertia::render('Admin/Users/Index', [
            'users' => $users,
            'keyword' => null,
        ]);
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
    public function search(Request $request): Response|RedirectResponse
    {
        $keyword = $request->keyword;

        if ($keyword!='') {
            $users = $this->user->where("name", "LIKE","%$keyword%")
                ->orWhere("email", "LIKE", "%$keyword%")
                ->orWhere("id", "=", "$keyword")
                ->paginate(15)
                ->through(static fn (User $user): array => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                ]);
            return Inertia::render('Admin/Users/Index', ['users' => $users, 'keyword' => $keyword]);
        }

        return back();
    }
    
    /**
     * @param int $id
     * @return View|RedirectResponse
     */
    public function showEditForm(int $id): Response|RedirectResponse
    {
        if ($id) {
            $user = $this->user->findOrFail($id);
        } else {
            return back()->withErrors('Invalid ID');
        }
        if (!$user instanceof User) {
            return back()->withErrors('User not found');
        }

        return Inertia::render('Admin/Users/Edit', [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ],
        ]);
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
