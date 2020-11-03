<?php

namespace App\Http\Controllers;

use App\Message;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        // select all users except logged in user
        $users = User::where('id', '!=', Auth::id())->get();
        return view('home', compact('users'));
    }

    public function getMessage($user_id)
    {
        $my_id = Auth::id();
        //getting all message for selected user
        //getting those message which is from = Auth::id() and to = user_id or from = user_id and to = Auth::id()
        $messages = Message::where(function ($query) use ($user_id, $my_id) {
            $query->where('from', $my_id)->where('to', $user_id);
        })->orWhere(function ($query) use ($user_id, $my_id) {
            $query->where('from', $user_id)->where('to', $my_id);
        })->get();  

        return view('messages.index', compact('messages'));
    }

    public function sendMessage(Request $request) {
        $from = Auth::id();
        $to = $request->receiver_id;
        $message = $request->message;

        $data = new Message();
        $data->from = $from;
        $data->to = $to;
        $data->message = $message;
        $data->is_read = 0; // message will be unread when sending message

        $data->save();
    }
}

