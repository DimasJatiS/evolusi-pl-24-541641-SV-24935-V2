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

    <hr style="margin: 2.5rem 0; border: 0; border-top: 1px solid #e2e8f0;">

    <section>
        <h3>Komentar ({{ $post->comments->count() }})</h3>

        <!-- Form Tambah Komentar -->
        <form action="{{ route('posts.comments.store', $post) }}" method="POST" style="margin-top: 1rem; margin-bottom: 2rem; background: #f8fafc; padding: 1.25rem; border-radius: 8px; border: 1px solid #e2e8f0;">
            @csrf
            <div class="form-group">
                <label for="author_name">Nama Anda</label>
                <input type="text" name="author_name" id="author_name" class="form-control" value="{{ old('author_name') }}" placeholder="Tuliskan nama Anda" required>
                @error('author_name')
                    <div class="error-text">{{ $message }}</div>
                @enderror
            </div>
            <div class="form-group">
                <label for="content">Komentar</label>
                <textarea name="content" id="content" rows="3" class="form-control" placeholder="Tuliskan komentar Anda..." required>{{ old('content') }}</textarea>
                @error('content')
                    <div class="error-text">{{ $message }}</div>
                @enderror
            </div>
            <button type="submit" class="btn btn-primary">Kirim Komentar</button>
        </form>

        <!-- Daftar Komentar -->
        <div style="display: flex; flex-direction: column; gap: 1rem;">
            @forelse($post->comments()->latest()->get() as $comment)
                <div style="padding: 1rem; border: 1px solid #e2e8f0; border-radius: 8px; background: white;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                        <strong>{{ $comment->author_name }}</strong>
                        <span style="font-size: 0.8rem; color: #64748b;">{{ $comment->created_at->format('d M Y H:i') }}</span>
                    </div>
                    <p style="margin: 0 0 0.75rem 0; color: #334155;">{{ $comment->content }}</p>
                    <form action="{{ route('comments.destroy', $comment) }}" method="POST" onsubmit="return confirm('Hapus komentar ini?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger" style="padding: 0.25rem 0.6rem; font-size: 0.75rem;">Hapus Komentar</button>
                    </form>
                </div>
            @empty
                <p style="color: #64748b; font-style: italic;">Belum ada komentar pada postingan ini.</p>
            @endforelse
        </div>
    </section>
@endsection
