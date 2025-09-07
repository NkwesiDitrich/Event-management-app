<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Application\UseCases\Admin\GetAllUsersUseCase;
use App\Application\UseCases\Admin\ManageUserRoleUseCase;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class UserController extends Controller
{
    private GetAllUsersUseCase $getAllUsersUseCase;
    private ManageUserRoleUseCase $manageUserRoleUseCase;

    public function __construct(
        GetAllUsersUseCase $getAllUsersUseCase,
        ManageUserRoleUseCase $manageUserRoleUseCase
    ) {
        $this->getAllUsersUseCase = $getAllUsersUseCase;
        $this->manageUserRoleUseCase = $manageUserRoleUseCase;
    }

    public function index(): View
    {
        $users = $this->getAllUsersUseCase->execute();
        
        return view('admin.users.index', compact('users'));
    }

    public function updateRole(Request $request, int $userId): RedirectResponse
    {
        $request->validate([
            'role' => 'required|in:admin,organizer,attendee'
        ]);

        try {
            $this->manageUserRoleUseCase->execute($userId, $request->role);
            
            return redirect()->route('admin.users')
                ->with('success', 'User role updated successfully!');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }
}
