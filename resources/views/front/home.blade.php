@extends('layouts.front')

@section('content')
<!--<header class="masthead" style="background-image: url('{{ asset('templates/frontoffice/assets/img/bg-masthead.jpg') }}'); background-size: cover; background-position: center;">
-->
<header class="masthead">    
<div class="container position-relative">
        <div class="row justify-content-center">
            <div class="col-xl-7 text-center text-white">
                <h1 class="mb-4">Donnez une seconde vie à vos vêtements</h1>
                <p class="lead mb-4">Déposez, faites réparer, transformez ou donnez : particuliers, ateliers et associations réunis sur une même plateforme.</p>
                @guest
                    <a href="{{ route('register') }}" class="btn btn-primary btn-lg">Rejoindre la communauté</a>
                @else
                    <a href="{{ route('profile.edit') }}" class="btn btn-primary btn-lg">Compléter mon profil</a>
                @endguest
            </div>
        </div>
    </div>
</header>

<section class="features-icons bg-light text-center">
    <div class="container">
        <div class="row">
            @foreach ([['bi-box-arrow-in-down','Déposer','Déposez vos vêtements dans un point de collecte ou auprès d’un atelier.'],
                       ['bi-scissors','Réparer / Transformer','Des ateliers locaux réparent ou transforment vos pièces.'],
                       ['bi-heart','Donner','Les associations redistribuent les vêtements à ceux qui en ont besoin.']] as [$icon,$title,$text])
                <div class="col-lg-4">
                    <div class="features-icons-item mx-auto mb-5 mb-lg-3">
                        <div class="features-icons-icon d-flex"><i class="bi {{ $icon }} m-auto text-primary"></i></div>
                        <h3>{{ $title }}</h3>
                        <p class="lead mb-0">{{ $text }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

<section class="testimonials text-center bg-white">
    <div class="container">
        <h2 class="mb-5">La communauté en chiffres</h2>
        <div class="row">
            <div class="col-lg-4"><h1 class="display-4">{{ $membres }}</h1><p class="lead">membres actifs</p></div>
            <div class="col-lg-4"><h1 class="display-4">{{ $ateliers }}</h1><p class="lead">ateliers partenaires</p></div>
            <div class="col-lg-4"><h1 class="display-4">{{ $associations }}</h1><p class="lead">associations</p></div>
        </div>
    </div>
</section>
@endsection