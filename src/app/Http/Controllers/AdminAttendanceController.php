<?php

namespace App\Http\Controllers;

use App\Models\Attendance;

class AdminAttendanceController extends Controller
{
    public function index()
    {
        // 全ユーザーの勤怠を取得
        $attendances = Attendance::with('user')
            ->orderBy('date', 'desc')
            ->get();

        return view('admin.attendances.index', compact('attendances'));
    }
}
