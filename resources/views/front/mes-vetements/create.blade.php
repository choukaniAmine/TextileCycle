@extends('layouts.front')
@section('title', 'Ajouter un vêtement')

@section('content')
@include('front._hero', [
    'badge' => '✨ Nouveau',
    'titre' => 'Ajouter un vêtement',
    'sous' => 'Quelques informations suffisent pour lui offrir une seconde vie.',
])

<section class="page-body">
    <div class="container" style="max-width: 860px;">
        <div class="rf-card p-4 p-md-5">
            <form method="POST" action="{{ route('mes-vetements.store') }}" enctype="multipart/form-data">
                @include('front.mes-vetements._form')
            </form>
        </div>
    </div>
</section>
@endsection