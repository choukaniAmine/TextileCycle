@extends('layouts.admin')

@section('content')
<h3>Modifier le vêtement</h3>
<form method="POST" action="{{ route('admin.vetements.update', $vetement) }}" enctype="multipart/form-data">
    @method('PUT')
    @include('admin.vetements._form')
</form>
@endsection