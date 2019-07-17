@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Список Чатов</div>
                    <ul class="navbar-nav ml-auto">
                        @foreach($chatsList as $chat)
                            <li class="nav-item">
                                <a href="{{ route('chat', $chat->id ) }}">
                                    {{ Auth::user()->isPatient() ? $chat->specialty->name : ''}}
                                    {{ Auth::user()->isDoctor() ? $chat->patient->surname: $chat->doctor->surname }}
                                    {{ Auth::user()->isDoctor() ? $chat->patient->name : $chat->doctor->name}}
                                    {{ Auth::user()->isDoctor() ? $chat->patient->patronymic : $chat->doctor->patronymic}}
                                    {{ Auth::user()->isDoctor() ? $chat->patient->date : $chat->doctor->date }}
                                    {{ Auth::user()->isDoctor() ? $chat->patient->city : $chat->doctor->city }}
                                </a>
                            </li>
                        @endforeach
                    </ul>



                </div>
            </div>
        </div>
    </div>
</div>
@endsection
