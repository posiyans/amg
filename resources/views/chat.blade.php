@extends('layouts.app')

@section('content')
    <div>
        <chat-component :user="{{ Auth::user() }}"></chat-component>
    </div>

@endsection
