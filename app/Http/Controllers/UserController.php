<?php

namespace App\Http\Controllers;

use App\Specialty;
use App\Ticket;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class UserController extends Controller
{
    //


    public function patientList(){
        $user = Auth::user();
        if ($user->role == 2) {
            $data = [];
            $patients = User::where('role', 1)->get();
            $data['patients'] = $patients;
            return view('patientList', $data);
        }
        return redirect('home');

    }

    public function doctorList(){

    }

    public function patientProfile($id){
        $user = Auth::user();
        if ($user->role == 2) {
            $data = [];
            $patient = User::findOrFail($id);
            $data['patient']= $patient;
            return view('patientProfile', $data);
        }
        return redirect('home');
    }


    public function createTicket(){
        $user = Auth::user();
        if ($user->role == 1) {
            $data = [];
            $specialtys = Specialty::all();
            $data['specialtys']=$specialtys;
            return view('createTicket', $data);
        }
        return redirect('home');
    }


    public function saveTicket(Request $request){
        $user = Auth::user();
        if ($user->role == 1 and $request->has('specialty_id')) {
            $ticket= new Ticket();
            $ticket->specialty_id = (int)$request->input('specialty_id');
            $ticket->patient_id = $user->id;
            $ticket->rom_token = Str::random(32);
            $ticket->save();
            return redirect('home');
        }
        return redirect('home');
    }
}
