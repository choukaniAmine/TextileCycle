<?php

namespace App\Exports;

use App\Models\Vetement;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class VetementsExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize
{
    public function __construct(private Builder $query) {}

    public function query(): Builder
    {
        return $this->query;
    }

    public function headings(): array
    {
        return ['Nom', 'Catégorie', 'Taille', 'État', 'Type', 'Description', 'Ajouté le'];
    }

    /** @param Vetement $v */
    public function map($v): array
    {
        return [
            $v->nom,
            $v->categorie?->nom,
            $v->taille,
            $v->etat->label(),
            $v->type->label(),
            $v->description,
            $v->created_at->format('d/m/Y'),
        ];
    }
}