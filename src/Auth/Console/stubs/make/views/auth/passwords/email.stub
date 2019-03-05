@extends('layouts.main')

@section('content')
    <h1>{{ __('Reset Password') }}</h1>
    @if(session('status'))
        <div class="alert">
            {{ session('status') }}
        </div>
    @endif
    @if($errors->any())
        <div class="alert">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    @if(isset($form))
        {!! $form->render() !!}
    @endif
@endsection