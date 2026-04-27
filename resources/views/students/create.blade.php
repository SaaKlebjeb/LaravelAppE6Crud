@extends('layouts.app')

@section('title', 'Create Student')

@section('content')
    <section class="rounded-2xl bg-white p-6 shadow-sm">
        <h2 class="mb-4 text-xl font-bold">Create Student</h2>

        <form method="POST" action="{{ route('students.store') }}">
            @csrf
            @include('students._form')
        </form>
    </section>
@endsection
