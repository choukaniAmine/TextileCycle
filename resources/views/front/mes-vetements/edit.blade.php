@extends('layouts.front')

@section('content')
<section class="py-5">
    <div class="container" style="max-width: 800px;">
        <h1 class="h2 fw-bold mb-4">Modifier le vêtement</h1>
        <form method="POST" action="{{ route('mes-vetements.update', $vetement) }}" enctype="multipart/form-data">
            @method('PUT')
            @include('front.mes-vetements._form')
        </form>
    </div>
</section>
@endsection