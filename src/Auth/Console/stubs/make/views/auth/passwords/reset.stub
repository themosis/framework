@extends('layouts.main')

@section('content')
    <h1>{{ __('Reset Password') }}</h1>
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