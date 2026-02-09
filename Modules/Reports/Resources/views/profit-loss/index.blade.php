@extends('layouts.app')

@section('title', __('reports::reports.profit_loss_title'))

@section('breadcrumb')
    <ol class="breadcrumb border-0 m-0">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('reports::reports.home') }}</a></li>
        <li class="breadcrumb-item active">{{ __('reports::reports.profit_loss_breadcrumb') }}</li>
    </ol>
@endsection

@section('content')
    <div class="container-fluid">
        <livewire:reports.profit-loss-report/>
    </div>
@endsection
