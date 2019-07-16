@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Список Пациентов</div>
                    <ul class="navbar-nav ml-auto">
                        @foreach($patients as $patient)
                            <li class="nav-item">
                                <a href="{{ route('patientProfile', $patient->id) }}">
                                    {{ $patient->surname }}
                                    {{ $patient->name }}
                                    {{ $patient->patronymic }}
                                    {{ $patient->date }}
                                    {{ $patient->city }}
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
