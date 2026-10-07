@extends('layouts.admin')

@section('content')
<h3>Modifier la catégorie</h3>
<form method="POST" action="{{ route('admin.categories.update', $categorie) }}">
    @method('PUT')
    @include('admin.categories._form')
</form>
@endsection