@extends('layouts.app')

@section('title', 'Edit Student')

@section('content')
    <section class="rounded-2xl bg-white p-6 shadow-sm">
        <h2 class="mb-4 text-xl font-bold">Edit Student: {{ $student->full_name }}</h2>

        <form method="POST" action="{{ route('students.update', $student) }}">
            @csrf
            @method('PUT')
            @include('students._form')
        </form>
    </section>
@endsection
