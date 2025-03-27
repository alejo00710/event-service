<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;

class EventController extends Controller
{
    // Listar todos los eventos
    public function index()
    {
        return response()->json(Event::all());
    }

    // Mostrar un evento por ID
    public function show($id)
    {
        $event = Event::find($id);
        if (!$event) {
            return response()->json(['message' => 'Evento no encontrado'], 404);
        }
        return response()->json($event);
    }

    // Crear un nuevo evento
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'date' => 'required|date',
            'location' => 'required|string|max:255',
            'capacity' => 'required|integer|min:1',
        ]);

        $event = Event::create($request->all());
        return response()->json($event, 201);
    }

    // Actualizar un evento
    public function update(Request $request, $id)
    {
        $event = Event::find($id);
        if (!$event) {
            return response()->json(['message' => 'Evento no encontrado'], 404);
        }

        $event->update($request->all());
        return response()->json($event);
    }

    // Eliminar un evento
    public function destroy($id)
    {
        $event = Event::find($id);
        if (!$event) {
            return response()->json(['message' => 'Evento no encontrado'], 404);
        }

        $event->delete();
        return response()->json(['message' => 'Evento eliminado']);
    }
}

