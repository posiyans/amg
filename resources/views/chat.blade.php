@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">Чат
                        {{ Auth::user()->isPatient() ? $chat->specialty->name : ''}}
                        {{ Auth::user()->isDoctor() ? $chat->patient->surname: $chat->doctor->surname }}
                        {{ Auth::user()->isDoctor() ? $chat->patient->name : $chat->doctor->name}}
                        {{ Auth::user()->isDoctor() ? $chat->patient->patronymic : $chat->doctor->patronymic}}
                    </div>
                    <div>
                        <chat-component :user="{{ Auth::user() }}" :room="{{ $chat }}"></chat-component>
                    </div>
                </div>

            </div>
        </div>
    </div>

@endsection
