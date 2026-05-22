<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\AgentProfile;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class AgentManagementController extends Controller
{
    public function index(): View
    {
        $agents = User::with('agentProfile')
            ->where('role', 'agent')
            ->orderBy('name')
            ->paginate(15);

        return view('admin.agents.index', ['agents' => $agents]);
    }

    public function create(): View
    {
        return view('admin.agents.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users'],
            'password' => ['required', 'min:8', 'confirmed'],
            'employee_code' => ['required', 'string', 'unique:agent_profiles'],
            'phone' => ['required', 'string', 'max:20'],
            'date_of_birth' => ['nullable', 'date', 'before:today'],
            'is_active' => ['boolean'],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'agent',
        ]);

        AgentProfile::create([
            'user_id'       => $user->id,
            'employee_code' => $validated['employee_code'],
            'phone'         => $validated['phone'],
            'date_of_birth' => $validated['date_of_birth'] ?? null,
            'is_active'     => $request->boolean('is_active', true),
        ]);

        return redirect()->route('admin.agents.edit', $user)->with('success', 'Agent created. You can now add payout information below.');
    }

    public function show(User $agent): View
    {
        if ($agent->role !== 'agent') {
            return back()->with('error', 'Invalid agent');
        }

        $agent->load(['agentProfile', 'assignedCustomers']);

        return view('admin.agents.show', ['agent' => $agent]);
    }

    public function edit(User $agent): View
    {
        if ($agent->role !== 'agent') {
            return back()->with('error', 'Invalid agent');
        }

        $agent->load('agentProfile');

        return view('admin.agents.edit', ['agent' => $agent]);
    }

    public function update(Request $request, User $agent): RedirectResponse
    {
        if ($agent->role !== 'agent') {
            return back()->with('error', 'Invalid agent');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', "unique:users,email,{$agent->id}"],
            'employee_code' => ['required', 'string', "unique:agent_profiles,employee_code,{$agent->agentProfile->id}"],
            'phone' => ['required', 'string', 'max:20'],
            'date_of_birth' => ['nullable', 'date', 'before:today'],
            'is_active' => ['boolean'],
        ]);

        $agent->update([
            'name'  => $validated['name'],
            'email' => $validated['email'],
        ]);

        $agent->agentProfile->update([
            'employee_code' => $validated['employee_code'],
            'phone'         => $validated['phone'],
            'date_of_birth' => $validated['date_of_birth'] ?? null,
            'is_active'     => $request->boolean('is_active', true),
        ]);

        return redirect()->route('admin.agents.index')->with('success', 'Agent updated successfully');
    }

    public function updateStatus(Request $request, User $agent): RedirectResponse
    {
        if ($agent->role !== 'agent') {
            return back()->with('error', 'Invalid agent');
        }

        $validated = $request->validate([
            'is_active' => ['required', 'boolean'],
        ]);

        $agent->agentProfile->update($validated);

        $status = $validated['is_active'] ? 'activated' : 'deactivated';

        return back()->with('success', "Agent {$status} successfully");
    }

    public function destroy(User $agent): RedirectResponse
    {
        if ($agent->role !== 'agent') {
            return back()->with('error', 'Invalid agent');
        }

        if ($agent->assignedCustomers()->exists()) {
            return back()->with('error', 'Cannot delete agent with assigned customers');
        }

        $agent->agentProfile->delete();
        $agent->delete();

        return redirect()->route('admin.agents.index')->with('success', 'Agent deleted successfully');
    }
}
