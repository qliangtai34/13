@extends('layouts.app')

@section('content')
<div class="container">

    <h2 class="mb-4">勤怠詳細</h2>

    {{-- ① 名前（ログインユーザー） --}}
    <div class="mb-3">
        <label class="form-label fw-bold">名前</label>
        <div>{{ Auth::user()->name }}</div>
    </div>

    {{-- ② 日付 --}}
    <div class="mb-3">
        <label class="form-label fw-bold">日付</label>
        <div>{{ $attendance->date->format('Y-m-d') }}</div>
    </div>

    {{-- ③ 出勤・退勤 --}}
    <div class="mb-3">
        <label class="form-label fw-bold">出勤・退勤</label>
        <div>
            出勤：{{ $attendance->clock_in ? $attendance->clock_in->format('H:i') : '-' }}<br>
            退勤：{{ $attendance->clock_out ? $attendance->clock_out->format('H:i') : '-' }}
        </div>
    </div>

    {{-- ④ 休憩（レコード数分＋追加 1 行） --}}
<div class="mb-3">
    <label class="form-label fw-bold">休憩</label>

    @forelse ($attendance->breaks as $index => $break)
        <div class="mb-2">
            <span class="fw-bold">休憩 {{ $index + 1 }}：</span>
            開始：{{ $break->break_start ? $break->break_start->format('H:i') : '-' }}
            ／
            終了：{{ $break->break_end ? $break->break_end->format('H:i') : '-' }}
        </div>
    @empty
        <div>休憩記録なし</div>
    @endforelse

    {{-- 追加1行（仕様のため） --}}
    <div class="mt-2 text-muted">
        ※ 次の休憩を追加できます
    </div>
</div>


    <a href="{{ route('attendance.index') }}" class="btn btn-secondary">戻る</a>

</div>
@endsection