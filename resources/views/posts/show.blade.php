@extends('layouts.app')

@section('title', $post->title)

@section('content')
    <a href="{{ route('posts.index') }}" class="btn btn-secondary" style="font-size: 0.85rem; margin-bottom: 1rem;">← Kembali ke Daftar</a>

    <h1>{{ $post->title }}</h1>
    <p style="color: #64748b; font-size: 0.875rem;">
        Dibuat pada: {{ $post->created_at->format('d M Y, H:i') }} |
        Status: <strong>{{ $post->is_published ? 'Published' : 'Draft' }}</strong>
    </p>

    <div style="margin-top: 1.5rem; line-height: 1.8;">
        {!! nl2br(e($post->content)) !!}
    </div>

    <div style="margin-top: 2rem; display: flex; gap: 0.5rem;">
        <a href="{{ route('posts.edit', $post) }}" class="btn btn-primary">Edit Post</a>
        <form action="{{ route('posts.destroy', $post) }}" method="POST" onsubmit="return confirm('Hapus post ini?');">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger">Hapus</button>
        </form>
    </div>
@endsection
