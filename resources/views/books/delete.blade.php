@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Delete Book</h1>
        <div class="alert alert-danger">
            <p>Are you sure you want to delete the book titled "{{ $book->title }}"?</p>
        </div>
        <form action="{{ route('books.destroy', $book->id) }}" method="POST">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger">Delete</button>
            <a href="{{ route('books.index') }}" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
@endsection
