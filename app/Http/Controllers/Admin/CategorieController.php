<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Categorie;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CategorieController extends Controller
{
    public function index()
    {
        $categories = Categorie::withCount('vetements')->orderBy('nom')->paginate(10);
        return view('admin.categories.index', compact('categories'));
    }

    public function create()
    {
        return view('admin.categories.create');
    }

    public function store(Request $request)
    {
        Categorie::create($this->validated($request));
        return redirect()->route('admin.categories.index')->with('success', 'Catégorie ajoutée.');
    }

    public function edit(Categorie $categorie)
    {
        return view('admin.categories.edit', compact('categorie'));
    }

    public function update(Request $request, Categorie $categorie)
    {
        $categorie->update($this->validated($request, $categorie));
        return redirect()->route('admin.categories.index')->with('success', 'Catégorie modifiée.');
    }

    public function destroy(Categorie $categorie)
    {
        if ($categorie->vetements()->exists()) {
            return back()->with('error', 'Impossible : cette catégorie contient des vêtements.');
        }
        $categorie->delete();
        return redirect()->route('admin.categories.index')->with('success', 'Catégorie supprimée.');
    }

    private function validated(Request $request, ?Categorie $categorie = null): array
    {
        return $request->validate([
            'nom' => ['required', 'string', 'max:255', Rule::unique('categories', 'nom')->ignore($categorie)],
            'description' => ['nullable', 'string'],
        ]);
    }
}