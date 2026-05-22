@extends('layouts.admin')

@section('title', 'Agents')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1>Agents Management</h1>
    <a href="{{ route('admin.agents.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Add Agent
    </a>
</div>

<!-- Agents Table -->
<div class="card">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Employee Code</th>
                    <th>Phone</th>
                    <th>Birthday</th>
                    <th>Status</th>
                    <th>Customers</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($agents as $agent)
                    <tr>
                        <td><strong>{{ $agent->name }}</strong></td>
                        <td>{{ $agent->email }}</td>
                        <td>{{ $agent->agentProfile->employee_code }}</td>
                        <td>{{ $agent->agentProfile->phone }}</td>
                        <td>
                            @if($agent->agentProfile->date_of_birth)
                                <div>{{ $agent->agentProfile->date_of_birth->format('d M Y') }}</div>
                                <small class="text-muted">{{ $agent->agentProfile->date_of_birth->age }} yrs</small>
                                @if($agent->agentProfile->date_of_birth->format('m-d') === now()->format('m-d'))
                                    <span class="badge bg-warning text-dark ms-1"><i class="bi bi-balloon-heart-fill"></i> Today!</span>
                                @elseif($agent->agentProfile->date_of_birth->copy()->setYear(now()->year)->isFuture() &&
                                        $agent->agentProfile->date_of_birth->copy()->setYear(now()->year)->diffInDays(now()) <= 7)
                                    <div><small class="text-success fw-semibold">In {{ (int) now()->diffInDays($agent->agentProfile->date_of_birth->copy()->setYear(now()->year)) }} days</small></div>
                                @endif
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td>
                            @if($agent->agentProfile->is_active)
                                <span class="badge bg-success">Active</span>
                            @else
                                <span class="badge bg-danger">Inactive</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge bg-info">{{ $agent->assignedCustomers->count() }}</span>
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('admin.agents.show', $agent) }}" class="btn btn-outline-info" title="View">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('admin.agents.edit', $agent) }}" class="btn btn-outline-primary" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form method="POST" action="{{ route('admin.agents.update-status', $agent) }}" class="d-inline" onsubmit="return confirm('Toggle agent status?');">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="is_active" value="{{ $agent->agentProfile->is_active ? 0 : 1 }}">
                                    <button type="submit" class="btn btn-outline-{{ $agent->agentProfile->is_active ? 'danger' : 'success' }} btn-sm" title="{{ $agent->agentProfile->is_active ? 'Deactivate' : 'Activate' }}">
                                        <i class="bi bi-{{ $agent->agentProfile->is_active ? 'lock' : 'unlock' }}"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted py-4">No agents found</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-4">
    {{ $agents->links() }}
</div>
@endsection
