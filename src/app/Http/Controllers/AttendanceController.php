<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\BreakTime;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $today = Carbon::today();

        $attendance = Attendance::firstOrCreate(
            ['user_id' => $user->id, 'date' => $today->toDateString()],
            ['status' => '勤務外']
        );

        return view('attendance.index', compact('attendance'));
    }

    // 出勤
    public function clockIn()
    {
        $attendance = $this->todayAttendance();

        if ($attendance->status !== '勤務外') {
            return back()->with('error', 'すでに出勤済みです');
        }

        $attendance->update([
            'clock_in' => Carbon::now(),
            'status'   => '出勤中',
        ]);

        return back();
    }

    // 休憩入
    public function breakStart()
    {
        $attendance = $this->todayAttendance();

        if ($attendance->status !== '出勤中') {
            return back()->with('error', '出勤中のみ休憩に入れます');
        }

        BreakTime::create([
            'attendance_id' => $attendance->id,
            'break_start'   => Carbon::now(),
        ]);

        $attendance->update(['status' => '休憩中']);

        return back();
    }

    // 休憩戻
    public function breakEnd()
    {
        $attendance = $this->todayAttendance();

        if ($attendance->status !== '休憩中') {
            return back()->with('error', '休憩中ではありません');
        }

        $break = BreakTime::where('attendance_id', $attendance->id)
            ->whereNull('break_end')
            ->latest()
            ->first();

        $break->update(['break_end' => Carbon::now()]);

        $attendance->update(['status' => '出勤中']);

        return back();
    }

    // 退勤
    public function clockOut()
    {
        $attendance = $this->todayAttendance();

        if ($attendance->status !== '出勤中') {
            return back()->with('error', '出勤中のみ退勤できます');
        }

        $attendance->update([
            'clock_out' => Carbon::now(),
            'status'    => '退勤済',
        ]);

        return back()->with('message', 'お疲れ様でした。');
    }

    private function todayAttendance()
    {
        return Attendance::where('user_id', auth()->id())
            ->where('date', Carbon::today()->toDateString())
            ->firstOrFail();
    }



    public function list($year = null, $month = null)
    {
    // 初期表示：今月
    if (!$year || !$month) {
        $year = now()->year;
        $month = now()->month;
    }

    // 月初と月末
    $start = "{$year}-{$month}-01";
    $end   = date("Y-m-t", strtotime($start));  // 月末

    // ログインユーザーの勤怠データを月単位で取得
    $attendances = Attendance::where('user_id', auth()->id())
        ->whereBetween('date', [$start, $end])
        ->orderBy('date', 'asc')
        ->get();

    return view('attendance.list', [
        'attendances' => $attendances,
        'year' => $year,
        'month' => $month,
        'prevYear' => date("Y", strtotime("-1 month", strtotime($start))),
        'prevMonth' => date("m", strtotime("-1 month", strtotime($start))),
        'nextYear' => date("Y", strtotime("+1 month", strtotime($start))),
        'nextMonth' => date("m", strtotime("+1 month", strtotime($start))),
    ]);
    }

    public function detail($date)
    {
    $attendance = Attendance::where('user_id', auth()->id())
        ->where('date', $date)
        ->firstOrFail();

    return view('attendance.detail', compact('attendance'));
    }

}