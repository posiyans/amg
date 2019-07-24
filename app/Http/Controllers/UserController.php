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


    /**
     * вывод списка пациентов
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Illuminate\View\View
     */
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

    /**
     * вывод списка врачей
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
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

    /**
     * вывод профиля пациента
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Illuminate\View\View
     */
    public function patientProfile($id)
    {
        $user = Auth::user();
        if ($user->isDoctor()) {
            $data = [];
            $patient = User::findOrFail($id);
            $data['patient'] = $patient;
            return view('patientProfile', $data);
        }
        return redirect('home');
    }

    /**
     * вывод профиля врача
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Illuminate\View\View
     */
    public function doctorProfile($id)
    {
        $user = Auth::user();
        $data = [];
        $doctor = $user->getProfile($id);
        if ($doctor) {
            $data['user'] = $doctor;
            return view('doctorProfile', $data);
        }
        return redirect('home');
    }

    /**
     * создать задачу
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Illuminate\View\View
     */
    public function createTicket()
    {
        $user = Auth::user();
        if ($user->isPatient()) {
            $data = [];
            $specialtys = Specialty::all();
            $data['specialtys'] = $specialtys;
            return view('createTicket', $data);
        }
        return redirect('home');
    }

    /**
     * сохрание задачи
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function saveTicket(Request $request)
    {
        $user = Auth::user();
        if ($user->isPatient() and $request->has('specialty_id')) {
            $ticket = new Ticket();
            $ticket->specialty_id = (int)$request->input('specialty_id');
            $ticket->patient_id = $user->id;
            $ticket->rom_token = Str::random(32);
            $ticket->save();
            return redirect(route('ticketList'));
        }
        return redirect('home');
    }

    /**
     * вывод списка заявок
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function ticketList()
    {
        $user = Auth::user();
        $data = [];
        $ticketList = $user->getOpenTicket();
        $data['ticketList'] = $ticketList;
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
    public function checkAuth(Request $request)
    {
        if ($request->has('firstUser')) {
            User::query()->update(['online'=> 0]);
        }
        Auth::user()->setOnline();
        return response()->json(['auth' => \Auth::check(), 'user_id' => \Auth::user()->id, 'data'=> $request->firstUser]);
    }

    /**
     * проверка разрешения на подпску на канал
     * @param $chanell
     * @return bool
     */
    public function checkSub($chanell)
    {
        $user = Auth::user();
        if ($chanell == 'laravel_database_new-message-user.' . $user->id . ':userMessage') {
            return response()->json(['auth' => \Auth::check(), 'c' => $chanell, 'access' => True]);
        }
        $chanell = str_replace('laravel_database_new-message-chat.', '', $chanell);
        $chanell = str_replace(':userMessage', '', $chanell);
        $ticket = Ticket::findOrFail((int)$chanell)->checkAccess();
        if ($ticket) {
            $access = true;
        }
        return response()->json(['auth' => \Auth::check(), 'c' => $chanell, 'access' => $access]);
    }

    /**
     * Вывод списка чатов
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function chatsList()
    {
        $data = [];
        $user = Auth::user();
        $chatsList = $user->getInWorkTicket();
        $data['chatsList'] = $chatsList;
        return view('chatsList', $data);
    }

    /**
     * окно чата
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function chat($id=false)
    {
        $data = [];
        $user = Auth::user();

        if ($id){
            $chat = Ticket::find($id);
            if ($chat) {
                if ($chat->doctor_id == null) {
                    if ($user->isDoctor()) {
                        $chat->doctor_id = $user->id;
                        $chat->save();
                    } else {
                        return redirect('home');
                    }
                }
                if ($chat->checkAccess()) {
                    $data['chat'] = $chat;
                    return view('chat', $data);
                }
        }
        }
        return view('chat');
    }

    /**
     * получение сообщение и рассылка его
     * @param Request $request
     */
    public function sendMessage(Request $request)
    {
        $user = Auth::user();
        $this->validatorMessage($request->all())->validate();
        $ticket = Ticket::find((int)$request->input('room_id'))->checkAccess();
        $ticket->setReadAllMessage();
        if ($ticket){
            $message = Message::create([
                'text' => $request->input('message'),
                'user_id' => $user->id,
                'ticket_id' => $request->input('room_id')
            ]);
            $message->allCount = $ticket->setReadAllMessage();
            $message->collocutor();
            event(new \App\Events\NewMessage($message));
        }
    }

    /**
     * @param array $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validatorMessage(array $data)
    {
        return Validator::make($data, [
            'message' => ['required', 'string'],
            'room_id' => ['required', 'integer'],
        ]);
    }


    /**
     * получить все сообщения для заявки
     * @param $id
     * @return bool|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function getMessage($id)
    {
        $ticket = Ticket::find($id)->checkAccess();
        $ticket->setReadAllMessage();
        if ($ticket) {
            return response(['data' => $ticket->message], 200);
        }
        return false;
    }


    /**
     * установить пользователя онлайн
     * @return \Illuminate\Http\JsonResponse
     */
    public function setOnlineStatusUser()
    {
        return response()->json(['auth' => Auth::user()->setOnline(), 'user_id' => \Auth::id()]);
    }

    /**
     * установить пользователя оффлайн
     * @return \Illuminate\Http\JsonResponse
     */
    public function setOfflineStatusUser()
    {
        return response()->json(['auth' => Auth::user()->setOffline(), 'user_id' => \Auth::id()]);
    }

    public function getUserList()
    {
        $user = Auth::user();
        $chatsList = $user->getInWorkTicket();
        if ($user->isPatient()) {
            foreach ($chatsList as $chat) {
                $chat->collocutor = $chat->doctor;
                $chat->noReadMessage = count($chat->message) - $chat->patient_count_message;
            }
        }
        if ($user->isDoctor()) {
            foreach ($chatsList as $chat) {
                $chat->collocutor = $chat->patient;
                $chat->noReadMessage = count($chat->message) - $chat->doctor_count_message;
            }
        }
        return response()->json(['userList' => $chatsList]);
    }
}

