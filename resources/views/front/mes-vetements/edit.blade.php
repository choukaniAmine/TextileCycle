@extends('layouts.front')
@section('title', 'Modifier le vêtement')

@section('content')
@include('front._hero', [
    'badge' => '✏️ Modification',
    'titre' => 'Modifier le vêtement',
    'sous' => $vetement->nom,
])

<section class="page-body">
    <div class="container" style="max-width: 860px;">
        <div class="rf-card p-4 p-md-5">
            <form method="POST" action="{{ route('mes-vetements.update', $vetement) }}" enctype="multipart/form-data">
                @method('PUT')
                @include('front.mes-vetements._form')
            </form>
        </div>
    </div>
</section>
@endsection