<?php

namespace App\Http\Controllers;

use App\Models\TiposCambio;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Http\Requests\TiposCambioRequest;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class TiposCambioController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $tiposCambios = TiposCambio::paginate();

        return view('tipos-cambio.index', compact('tiposCambios'))
            ->with('i', ($request->input('page', 1) - 1) * $tiposCambios->perPage());
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $tiposCambio = new TiposCambio();

        return view('tipos-cambio.create', compact('tiposCambio'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(TiposCambioRequest $request): RedirectResponse
    {
        TiposCambio::create($request->validated());

        return Redirect::route('tipos-cambios.index')
            ->with('success', 'TiposCambio created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id): View
    {
        $tiposCambio = TiposCambio::find($id);

        return view('tipos-cambio.show', compact('tiposCambio'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id): View
    {
        $tiposCambio = TiposCambio::find($id);

        return view('tipos-cambio.edit', compact('tiposCambio'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(TiposCambioRequest $request, TiposCambio $tiposCambio): RedirectResponse
    {
        $tiposCambio->update($request->validated());

        return Redirect::route('tipos-cambios.index')
            ->with('success', 'TiposCambio updated successfully');
    }

    public function destroy($id): RedirectResponse
    {
        TiposCambio::find($id)->delete();

        return Redirect::route('tipos-cambios.index')
            ->with('success', 'TiposCambio deleted successfully');
    }
}
