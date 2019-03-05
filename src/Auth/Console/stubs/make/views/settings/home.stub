@extends('layouts.main')

@section('content')
    <h1>{{ __('Dashboard') }}</h1>
    @if (session('status'))
        <div class="alert alert-success" role="alert">
            {{ session('status') }}
        </div>
    @endif
    @if(session('verified'))
        <div class="alert alert-success" role="alert">
            {{ __('Your e-mail address has been verified. Thank you!') }}
        </div>
    @endif
    <p>Welcome {{ Auth::user()->name }}</p>
    <ul>
        <li>
            <a href="{{ route('logout') }}"
               onclick="event.preventDefault();document.getElementById('logout-form').submit();"
               title="{{ __('Logout') }}">{{ __('Logout') }}</a>
            {!! $form->render() !!}
        </li>
    </ul>
@endsection