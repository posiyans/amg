@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Профиль пациента: {{ $patient->surname }} {{ $patient->name }} {{ $patient->patronymic  }}</div>

                <div class="card-body">
                    <ul class="navbar-nav ml-auto">
                        <li class="nav-item">
                            Фамилия: {{ $patient->surname }}
                        </li>
                        <li class="nav-item">
                            Имя: {{ $patient->name }}
                        </li>
                        <li class="nav-item">
                            Отчество: {{ $patient->patronymic  }}
                        </li>
                        <li class="nav-item">
                            Дата рождения: {{ $patient->date }}
                        </li>
                        <li class="nav-item">
                            Город: {{ $patient->city }}
                        </li>
                    </ul>
                </div>
                <div class="card-body">
                    <div class="card-header">Заявки:</div>
                    <ul class="navbar-nav ml-auto">
                        @foreach($patient->patient_tickets as  $ticket)
                            <li class="nav-item">
                                {{ $ticket->created_at }} {{ $ticket->specialty->name }}
                                @if($ticket->doctor_id == null and $ticket->specialty_id == Auth::user()->specialty_id)
                                    <a href="">Начать чат</a>
                                @endif
                                @if($ticket->doctor_id == Auth::id())
                                    <a href="">Продолжить чат</a>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
