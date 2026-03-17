<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserAccountRequest;
use App\Http\Requests\UpdateUserAccountRequest;
use App\Models\College;
use App\Models\Department;
use App\Models\UserAccount;
use App\Services\UserAccountService;
use Exception;
use Illuminate\Http\Request;
use Inertia\Inertia;

class UserAccountController extends Controller
{
    public function __construct(
        protected UserAccountService $userAccountService,
    ) {}

    public function index(Request $request)
    {
        $perPage = 10;
        $search = $request->input('search');

        $users = $this->userAccountService->getUserAccount($perPage, $search);
        $colleges = College::all();
        $departments = Department::all();

        return Inertia::render('UserAccounts', [
            'users' => $users,
            'colleges' => $colleges,
            'departments' => $departments,
        ]);
    }

    public function store(StoreUserAccountRequest $request)
    {
        try {
            UserAccount::create($request->validated());

            return redirect()
                ->back()
                ->with('success', 'User account created successfully.');
        } catch (Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Failed to create user account. Please try again.');
        }
    }

    public function update(UpdateUserAccountRequest $request, UserAccount $userAccount)
    {
        try {
            $userAccount->update($request->validated());

            return redirect()
                ->back()
                ->with('success', 'User account updated successfully.');
        } catch (Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Failed to update user account. Please try again.');
        }
    }

    public function destroy(UserAccount $userAccount)
    {
        try {
            $userAccount->delete();

            return redirect()
                ->back()
                ->with('success', 'User account deleted successfully.');
        } catch (Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Failed to delete user account. It may be in use.');
        }
    }
}
