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
                    <ul class="navbar-nav ml-auto">
                        @foreach($patient->patien_tickets as  $ticket)
                            <li class="nav-item">
                                Заявка: {{ $ticket->specialty_id }}
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
