@extends('layouts.front')

@section('content')
<section class="py-5">
    <div class="container" style="max-width: 800px;">
        <h1 class="h2 fw-bold mb-4">Ajouter un vêtement</h1>
        <form method="POST" action="{{ route('mes-vetements.store') }}" enctype="multipart/form-data">
            @include('front.mes-vetements._form')
        </form>
    </div>
</section>
@endsection