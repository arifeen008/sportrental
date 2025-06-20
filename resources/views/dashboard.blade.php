@extends('layouts.app')
@section('content')
    <div class="container">
        <h1>Welcome to the Dashboard!</h1>
        <p>Hello, {{ $user->name ?? 'Guest' }}!</p>
        <p>You are successfully logged in.</p>

        <div class="logout-form">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit">Logout</button>
            </form>
        </div>
    </div>
@endsection
