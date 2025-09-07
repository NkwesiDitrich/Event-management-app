<?php

namespace App\Http\Controllers;

use App\Application\UseCases\User\UpdateProfileUseCase;
use App\Application\DTOs\UpdateProfileDTO;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ProfileController extends Controller
{
    private UpdateProfileUseCase $updateProfileUseCase;

    public function __construct(UpdateProfileUseCase $updateProfileUseCase)
    {
        $this->updateProfileUseCase = $updateProfileUseCase;
    }

    public function show(): View
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        return view('profile.show', ['user' => $user]);
    }

    public function update(Request $request): RedirectResponse
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        
        if (!$user) {
            return redirect()->route('login');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->getId(),
        ]);

        try {
            $dto = new UpdateProfileDTO(
                $request->name,
                $request->email
            );

            $this->updateProfileUseCase->execute($user->getId(), $dto);

            return redirect()->route('profile')->with('success', 'Profile updated successfully!');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }
}
