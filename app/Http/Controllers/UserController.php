<?php

namespace App\Http\Controllers;

use App\Message;
use App\Specialty;
use App\Ticket;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class UserController extends Controller
{
    //


    public function patientList()
    {
        $user = Auth::user();
        if ($user->isDoctor()) {
            $data = [];
            $patients = User::where('role', 1)->get();
            $data['patients'] = $patients;
            return view('patientList', $data);
        }
        return redirect('home');

    }

    public function doctorList()
    {
        $user = Auth::user();
        $data = [];
        if ($user->isPatient()) {
            $doctors = $user->getDoctorForPatient();
        }
        if ($user->isDoctor()) {
            $doctors = User::getAllDoctor();
        }
        $data['doctors'] = $doctors;
        return view('doctorList', $data);
    }

    public function patientProfile($id)
    {
        $user = Auth::user();
        if ($user->role == 2) {
            $data = [];
            $patient = User::findOrFail($id);
            $data['patient'] = $patient;
            return view('patientProfile', $data);
        }
        return redirect('home');
    }

    public function doctorProfile($id)
    {
        $user = Auth::user();
        $data = [];
        $doctor = $user->getProfile($id);
        //dd($doctor);
        if ($doctor) {
            $data['user'] = $doctor;
            return view('doctorProfile', $data);
        }
        return redirect('home');
    }

    public function createTicket()
    {
        $user = Auth::user();
        if ($user->role == 1) {
            $data = [];
            $specialtys = Specialty::all();
            $data['specialtys'] = $specialtys;
            return view('createTicket', $data);
        }
        return redirect('home');
    }


    public function saveTicket(Request $request)
    {
        $user = Auth::user();
        if ($user->role == 1 and $request->has('specialty_id')) {
            $ticket = new Ticket();
            $ticket->specialty_id = (int)$request->input('specialty_id');
            $ticket->patient_id = $user->id;
            $ticket->rom_token = Str::random(32);
            $ticket->save();
            return redirect('home');
        }
        return redirect('home');
    }


    public function ticketList()
    {
        $user = Auth::user();
        $data = [];
        $ticketList = $user->getOpenTicket();
        $data['ticketList'] = $ticketList;
        //$l = $user->patient_tickets;
        //dump($user->hasMany('App\Ticket', 'patient_id', 'id'));
        if ($user->isPatient()) {
            return view('ticketListPatient', $data);
        }
        if ($user->isDoctor()) {
            return view('ticketList', $data);
        }

    }

    /**
     * проверка на авторизона пользователь или нет
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkAuth()
    {
        return response()->json(['auth' => \Auth::check()]);
    }

    /**
     * проверка разрешения на подпску на канал
     * @param $chanell
     * @return bool
     */
    public function checkSub($chanell)
    {
        return true;
    }


    public function chatsList()
    {
        $data = [];
        $user = Auth::user();
        $chatsList = $user->getInWorkTicket();
        $data['chatsList'] = $chatsList;
        return view('chatsList', $data);
    }

    public function chat($id)
    {
        $data = [];
        $user = Auth::user();
        $chat = Ticket::find($id);
        if ($chat->doctor_id == null) {
            $chat->doctor_id = $user->id;
            $chat->save();
        }
        $data['chat'] = $chat;
        return view('chat', $data);
//        $data = [];
//        $user = Auth::user();
//        $chatsList = $user->getInWorkTicket();
//        $data['chatsList'] = $chatsList;
//        return view('chatsList', $data);
    }


    public function sendMessage(Request $request)
    {
        $user = Auth::user();
        $this->validatorMessage($request->all())->validate();
        $messge = Message::create([
            'text'=>$request->input('message'),
            'user_id'=>$user->id,
            'ticket_id'=>$request->input('room_id')
        ]);
        event(new \App\Events\NewMessage($messge));
        dump($messge);
        dump($request);
    }


    protected function validatorMessage(array $data)
    {
        return Validator::make($data, [
            'message' => ['required', 'string'],
            'room_id' => ['required', 'integer'],
        ]);


    }


    public function getMessage($id)
    {
        $user = Auth::user();
        $ticket = Ticket::find($id)->checkAccess();
        if ($ticket){
            return response(['data'=>$ticket->message], 200 );
        }
        return false;

    }
}
