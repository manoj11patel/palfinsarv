@extends('layouts.admin')

@section('title', 'Message Thread')

@section('content')
<div class="mb-4">
    <a href="{{ route('admin.messages.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Back to Messages
    </a>
</div>

<!-- Message Thread -->
<div class="card mb-4">
    <div class="card-header bg-light d-flex justify-content-between align-items-center">
        <div>
            <h5 class="mb-0">conversation with {{ $otherUser->name }}</h5>
            <small class="text-muted">{{ $otherUser->email }}</small>
        </div>
        <span class="badge bg-info">{{ $thread->count() }} messages</span>
    </div>

    <!-- Message Thread Display -->
    <div class="card-body" style="max-height: 500px; overflow-y: auto; background-color: #f9f9f9;">
        @foreach($thread as $msg)
            <div class="mb-4">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div>
                        <strong>{{ $msg->sender->name }}</strong>
                        @if($msg->sender->role === 'agent')
                            <span class="badge bg-warning">Agent</span>
                        @elseif($msg->sender->role === 'admin')
                            <span class="badge bg-danger">Admin</span>
                        @endif
                    </div>
                    <small class="text-muted">{{ $msg->created_at->format('M d, Y h:i A') }}</small>
                </div>
                
                @if($msg->subject)
                    <div class="mb-2">
                        <strong>Subject:</strong> {{ $msg->subject }}
                    </div>
                @endif

                <div class="p-3 bg-white border rounded" style="border-left: 4px solid @if($msg->sender->role === 'admin') #dc3545 @else #ffc107 @endif;">
                    {{ nl2br(e($msg->content)) }}
                </div>
            </div>
        @endforeach
    </div>
</div>

<!-- Reply Form -->
<div class="card">
    <div class="card-header bg-light">
        <h5 class="mb-0">Send Reply</h5>
    </div>
    <div class="card-body">
        @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <form action="{{ route('admin.messages.reply', $parentMessage) }}" method="POST">
            @csrf

            <div class="mb-3">
                <label for="content" class="form-label">Message <span class="text-danger">*</span></label>
                <textarea 
                    class="form-control @error('content') is-invalid @enderror" 
                    id="content" 
                    name="content" 
                    rows="5" 
                    placeholder="Type your reply here..."
                    required></textarea>
                @error('content')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-send"></i> Send Reply
                </button>
                <a href="{{ route('admin.messages.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-x"></i> Cancel
                </a>
            </div>
        </form>
    </div>
</div>

@endsection
