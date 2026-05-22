@extends('layouts.admin')

@section('title', 'Messages')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1>Messages</h1>
    <span class="badge bg-info">{{ $messages->total() }} Messages</span>
</div>

<!-- Search & Filter -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="row g-3">
            <div class="col-md-6">
                <input type="text" name="search" class="form-control" placeholder="Search by subject or content" value="{{ request('search') }}">
            </div>
            <div class="col-md-4">
                <select name="sender_id" class="form-select">
                    <option value="">All Agents</option>
                    @foreach($agents as $agent)
                        <option value="{{ $agent->id }}" @if(request('sender_id') == $agent->id) selected @endif>
                            {{ $agent->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-search"></i> Search
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Messages Table -->
<div class="card">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>From</th>
                    <th>Subject</th>
                    <th>Message Preview</th>
                    <th>Status</th>
                    <th>Received</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($messages as $message)
                    <tr class="@if(!$message->is_read) table-warning @endif">
                        <td>
                            <strong>{{ $message->sender->name }}</strong>
                            <br>
                            <small class="text-muted">{{ $message->sender->email }}</small>
                        </td>
                        <td>
                            @if($message->subject)
                                <strong>{{ Str::limit($message->subject, 50) }}</strong>
                            @else
                                <em class="text-muted">No subject</em>
                            @endif
                        </td>
                        <td>
                            {{ Str::limit($message->content, 100) }}
                        </td>
                        <td>
                            @if(!$message->is_read)
                                <span class="badge bg-danger">Unread</span>
                            @else
                                <span class="badge bg-success">Read</span>
                            @endif
                        </td>
                        <td>{{ $message->created_at->format('M d, Y h:i A') }}</td>
                        <td>
                            <a href="{{ route('admin.messages.show', $message) }}" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-reply"></i> View & Reply
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-4">
                            <p class="text-muted mb-0">No messages found.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Pagination -->
<nav aria-label="Page navigation" class="mt-4">
    {{ $messages->links('pagination::bootstrap-5') }}
</nav>
@endsection
