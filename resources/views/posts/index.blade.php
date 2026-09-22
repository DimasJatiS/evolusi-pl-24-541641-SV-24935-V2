@extends('layouts.app')

@section('title', 'Daftar Post')

@section('content')
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
        <h1 style="margin: 0;">Daftar Post</h1>
        <a href="{{ route('posts.create') }}" class="btn btn-primary">+ Tambah Post Baru</a>
    </div>

    @if ($posts->isEmpty())
        <p>Belum ada post yang tersedia.</p>
    @else
        <table>
            <thead>
                <tr>
                    <th>Judul</th>
                    <th>Status</th>
                    <th>Tanggal Dibuat</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($posts as $post)
                    <tr>
                        <td>
                            <a href="{{ route('posts.show', $post) }}" style="font-weight: 600; text-decoration: none; color: #2563eb;">
                                {{ $post->title }}
                            </a>
                        </td>
                        <td>
                            <span class="badge {{ $post->is_published ? 'badge-success' : 'badge-secondary' }}">
                                {{ $post->is_published ? 'Published' : 'Draft' }}
                            </span>
                        </td>
                        <td>{{ $post->created_at->format('d M Y') }}</td>
                        <td style="display: flex; gap: 0.5rem;">
                            <a href="{{ route('posts.edit', $post) }}" class="btn btn-secondary" style="font-size: 0.8rem; padding: 0.25rem 0.5rem;">Edit</a>
                            <form action="{{ route('posts.destroy', $post) }}" method="POST" onsubmit="return confirm('Hapus post ini?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger" style="font-size: 0.8rem; padding: 0.25rem 0.5rem;">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div style="margin-top: 1.5rem;">
            {{ $posts->links() }}
        </div>
    @endif
@endsection
