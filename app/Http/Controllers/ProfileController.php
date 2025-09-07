<?php

namespace App\Http\Controllers;

use App\Application\UseCases\User\UpdateProfileUseCase;
use App\Application\DTOs\UpdateProfileDTO;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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
        return view('profile.show', ['user' => auth()->user()]);
    }

    public function update(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . auth()->id(),
        ]);

        try {
            $dto = new UpdateProfileDTO(
                $request->name,
                $request->email
            );

            $this->updateProfileUseCase->execute(auth()->id(), $dto);

            return redirect()->route('profile')->with('success', 'Profile updated successfully!');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }
}