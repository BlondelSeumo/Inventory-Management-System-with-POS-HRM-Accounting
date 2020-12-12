<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Notifications\SendNotification;
use Auth;

class NotificationController extends Controller
{
    public function store(Request $request)
    {
    	$user = User::find($request->user_id);
    	$user->notify(new SendNotification($request));
    	return redirect()->back()->with('message', 'Notification send successfully');
    }

    public function markAsRead()
    {
    	Auth::user()->unreadNotifications->markAsRead();
    }
}
