<?php

namespace App\Http\Controllers;

use App\Models\Reserva;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Http\Requests\ReservaRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ReservaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $reservas = Reserva::where('Usuario_id', Auth::id())
        ->orderBy('created_at', 'desc')
        ->paginate();

        return view('reserva.index', compact('reservas'))
            ->with('i', ($request->input('page', 1) - 1) * $reservas->perPage());
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $reserva = new Reserva();

        return view('reserva.create', compact('reserva'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ReservaRequest $request): RedirectResponse
    {
        Reserva::create($request->validated());

        return Redirect::route('reservas.index')
            ->with('success', 'Reserva created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id): View
    {
        $reserva = Reserva::find($id);

        return view('reserva.show', compact('reserva'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id): View
    {
        $reserva = Reserva::find($id);

        return view('reserva.edit', compact('reserva'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ReservaRequest $request, Reserva $reserva): RedirectResponse
    {
        $reserva->update($request->validated());

        return Redirect::route('reservas.index')
            ->with('success', 'Reserva updated successfully');
    }

    public function destroy($id): RedirectResponse
    {
        Reserva::find($id)->delete();

        return Redirect::route('reservas.index')
            ->with('success', 'Reserva deleted successfully');
    }
}
