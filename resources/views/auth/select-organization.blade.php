@extends('layouts.auth', ['title' => 'Select Organization'])

@section('content')
    <div class="mb-6 text-center">
        <h2 class="text-xl font-bold text-slate-900">Select Organization</h2>
        <p class="text-xs text-slate-500 mt-1">Choose which business account you wish to manage</p>
    </div>

    @if ($errors->any())
        <div class="mb-4 p-3.5 bg-rose-50 border border-rose-200 rounded-xl text-xs text-rose-700 font-medium">
            @foreach ($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <form method="POST" action="{{ route('organization.select.submit') }}" class="space-y-3">
        @csrf

        @foreach($memberships as $membership)
            @php $org = $membership->organization; @endphp
            <label class="flex items-center p-4 border border-slate-200 rounded-xl hover:border-teal-500 hover:bg-teal-50/50 cursor-pointer transition-all">
                <input type="radio" name="organization_id" value="{{ $org->public_id }}" class="w-4 h-4 text-teal-600 focus:ring-teal-500" required>
                <div class="ms-3 flex-1">
                    <div class="font-bold text-slate-900 text-sm">{{ $org->name }}</div>
                    <div class="text-xs text-slate-500">{{ $org->slug }} &bull; {{ ucfirst($org->status->value) }}</div>
                </div>
                <div class="w-7 h-7 rounded-lg bg-teal-100 text-teal-700 flex items-center justify-center font-bold text-xs">
                    {{ strtoupper(substr($org->name, 0, 2)) }}
                </div>
            </label>
        @endforeach

        <button type="submit"
            class="w-full py-3 px-4 bg-teal-600 hover:bg-teal-700 text-white font-bold text-sm rounded-xl shadow-lg shadow-teal-600/30 transition-all duration-150 transform active:scale-[0.98] mt-4">
            Continue to Panel
        </button>
    </form>
@endsection
