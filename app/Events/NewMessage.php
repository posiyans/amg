<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use App\User;
use Mail;

class NewMessage implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $text;
    public $ticket_id;
    public $user_id;
    public $created_at;
    public $collocutor_id;
    public $id;
    public $count;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($data)
    {
        $this->user_id = $data->user_id;
        $this->ticket_id = $data->ticket_id;
        $this->text = $data->text;
        $this->id = $data->id;
        $this->created_at = $data->created_at;
        $this->collocutor_id = $data->collocutor_id;
        $this->count = $data->allCount;


    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        $collocutor = User::find($this->collocutor_id);
        if (!$collocutor->isOnline()) {
//            Mail::raw('Вам пришло сообщение в чате "'.$this->text.'"', function($message) use ($collocutor)
//            {
//                $message->to($collocutor->email)->subject('Новое сообщение');
//            });
            $this->text.='   (send offline)';
        }
        return ['new-message-chat.' . $this->ticket_id, 'new-message-user.' . $this->collocutor_id];
    }

    public function broadcastAs()
    {
        return 'userMessage';
    }
}
