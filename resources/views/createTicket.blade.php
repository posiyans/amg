@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Подать заявку на чат со специалистом</div>
                <div class="card-body">
                    <form method="POST" action="{{ route('saveTicket') }}">
                        @csrf
                        <div class="form-group row">
                            <div class="col-md-6">
                                <label>Выберити специализацию</label>
                                <select class="form-control" name="specialty_id" >
                                    <option value="" selected disabled>Выбрать специализацию</option>
                                    @foreach($specialtys as $specialty)
                                        <option value="{{ $specialty->id }}">{{ $specialty->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="form-group row mb-0">
                                <div class="col-md-6 offset-md-4">
                                    <button type="submit" class="btn btn-primary">
                                        Подать
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
