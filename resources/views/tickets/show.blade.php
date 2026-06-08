@extends('pub_theme::layouts.app')

@section('content')
@php
    $ticket = $row;
    $latitude = $ticket->latitude;
    $longitude = $ticket->longitude;
@endphp

<div class="container py-4 py-md-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            @if(session('status'))
                <div class="alert alert-success" role="alert">
                    {{ session('status') }}
                </div>
            @endif

            {{-- Ticket Info Card --}}
            <article class="card mb-4 shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h1 class="h4 mb-0">
                        Ticket #{{ $ticket->id }}: {{ $ticket->title }}
                    </h1>
                </div>

                <div class="card-body">
                    <div class="row g-4">
                        {{-- Left column: Details --}}
                        <div class="col-lg-8">
                            <dl class="row mb-0">
                                <dt class="col-sm-3 text-muted">{{ trans('cruds.ticket.fields.content') }}</dt>
                                <dd class="col-sm-9">{!! $ticket->content !!}</dd>

                                @if($ticket->attachments->isNotEmpty())
                                    <dt class="col-sm-3 text-muted">{{ trans('cruds.ticket.fields.attachments') }}</dt>
                                    <dd class="col-sm-9">
                                        <ul class="list-unstyled mb-0">
                                            @foreach($ticket->attachments as $attachment)
                                                <li>
                                                    <a href="{{ $attachment->getUrl() }}" class="text-decoration-none">
                                                        {{ $attachment->file_name }}
                                                    </a>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </dd>
                                @endif
                            </dl>
                        </div>

                        {{-- Right column: Map --}}
                        <div class="col-lg-4">
                            @include('pub_theme::components.ticket-location-map', [
                                'latitude' => $latitude,
                                'longitude' => $longitude,
                                'ticketTitle' => $ticket->title,
                            ])
                        </div>
                    </div>
                </div>

                <div class="card-footer bg-light">
                    <div class="row text-muted small">
                        <div class="col-sm-6">
                            <strong>Status:</strong>
                            <span class="badge bg-{{ $ticket->status->color ?? 'secondary' }}">
                                {{ $ticket->status->name ?? 'Unknown' }}
                            </span>
                        </div>
                        <div class="col-sm-6 text-sm-end">
                            <strong>{{ trans('cruds.ticket.fields.author_name') }}:</strong>
                            {{ $ticket->author_name }}
                        </div>
                    </div>
                </div>
            </article>

            {{-- Comments Section --}}
            @include('pub_theme::components.ticket-comments', ['ticket' => $ticket])
        </div>
    </div>
</div>
@endsection
