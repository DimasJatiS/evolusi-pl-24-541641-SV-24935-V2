@extends('layouts.app')

@section('title', 'Tambah Post Baru')

@section('content')
    <h1>Tambah Post Baru</h1>

    <form action="{{ route('posts.store') }}" method="POST">
        @csrf

        <div class="form-group">
            <label for="title">Judul Post</label>
            <input type="text" id="title" name="title" class="form-control" value="{{ old('title') }}" required>
            @error('title')
                <div class="error-text">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="content">Konten</label>
            <textarea id="content" name="content" rows="6" class="form-control" required>{{ old('content') }}</textarea>
            @error('content')
                <div class="error-text">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label>
                <input type="checkbox" name="is_published" value="1" {{ old('is_published') ? 'checked' : '' }}>
                Publikasikan sekarang
            </label>
        </div>

        <div style="display: flex; gap: 0.5rem;">
            <button type="submit" class="btn btn-primary">Simpan Post</button>
            <a href="{{ route('posts.index') }}" class="btn btn-secondary">Batal</a>
        </div>
    </form>
@endsection
