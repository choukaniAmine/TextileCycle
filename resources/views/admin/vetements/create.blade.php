@extends('layouts.admin')

@section('content')
<h3>Ajouter un vêtement</h3>
<form method="POST" action="{{ route('admin.vetements.store') }}" enctype="multipart/form-data">
    @include('admin.vetements._form')
</form>
@endsection