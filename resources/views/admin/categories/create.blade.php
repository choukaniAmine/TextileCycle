@extends('layouts.admin')

@section('content')
<h3>Ajouter une catégorie</h3>
<form method="POST" action="{{ route('admin.categories.store') }}">
    @include('admin.categories._form')
</form>
@endsection