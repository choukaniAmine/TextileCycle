@extends('layouts.admin')
@section('title', 'Tableau de bord')
@section('content')
<div class="row">
    <div class="col-md-3 col-6"><div class="small-box bg-info"><div class="inner"><h3>{{ $total }}</h3><p>Utilisateurs</p></div><div class="icon"><i class="fas fa-users"></i></div></div></div>
    <div class="col-md-3 col-6"><div class="small-box bg-success"><div class="inner"><h3>{{ $actifs }}</h3><p>Comptes actifs</p></div><div class="icon"><i class="fas fa-user-check"></i></div></div></div>
    <div class="col-md-3 col-6"><div class="small-box bg-warning"><div class="inner"><h3>{{ $parRole['atelier'] }}</h3><p>Ateliers</p></div><div class="icon"><i class="fas fa-cut"></i></div></div></div>
    <div class="col-md-3 col-6"><div class="small-box bg-danger"><div class="inner"><h3>{{ $parRole['association'] }}</h3><p>Associations</p></div><div class="icon"><i class="fas fa-hands-helping"></i></div></div></div>
<div class="col-md-3 col-6"><div class="small-box bg-primary"><div class="inner"><h3>{{ $demandes }}</h3><p>Demandes</p></div><div class="icon"><i class="fas fa-cut"></i></div></div></div>
<div class="col-md-3 col-6"><div class="small-box bg-teal"><div class="inner"><h3>{{ $demandesEnCours }}</h3><p>En cours</p></div><div class="icon"><i class="fas fa-spinner"></i></div></div></div>
</div>
<div class="card">
    <div class="card-header"><h3 class="card-title">Dernières inscriptions</h3></div>
    <div class="card-body p-0">
        <table class="table table-sm">
            <thead><tr><th>Nom</th><th>E-mail</th><th>Rôle</th><th>Inscrit le</th></tr></thead>
            <tbody>
            @foreach ($derniers as $u)
                <tr><td>{{ $u->name }}</td><td>{{ $u->email }}</td>
                    <td><span class="badge badge-{{ $u->role->badge() }}">{{ $u->role->label() }}</span></td>
                    <td>{{ $u->created_at->format('d/m/Y') }}</td></tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
