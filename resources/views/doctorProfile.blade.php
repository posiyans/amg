@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Профиль врача: {{ $user->surname }} {{ $user->name }} {{ $user->patronymic  }}</div>

                <div class="card-body">
                    <ul class="navbar-nav ml-auto">
                        <li class="nav-item">
                            Фамилия: {{ $user->surname }}
                        </li>
                        <li class="nav-item">
                            Имя: {{ $user->name }}
                        </li>
                        <li class="nav-item">
                            Отчество: {{ $user->patronymic  }}
                        </li>
                        <li class="nav-item">
                            Дата рождения: {{ $user->date }}
                        </li>
                        <li class="nav-item">
                            Город: {{ $user->city }}
                        </li>
                        <li class="nav-item">
                            Специализация: {{ $user->specialty->name }}
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
