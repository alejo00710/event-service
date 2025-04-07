<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class EventController extends Controller
{
    // Listar todos los eventos
    public function index(Request $request)
    {
        // 1. Leer el ID del usuario desde los encabezados
        $userId = $request->header('X-User-ID');

        // 2. Validar si se envió el encabezado
        if (!$userId) {
            return response()->json(['error' => 'Falta el encabezado X-User-ID'], 400);
        }


        // 3. Hacer la solicitud al microservicio de usuarios para validar el usuario
        $userResponse = Http::get("http://localhost:8001/api/users/$userId");


        // 4. Validar si la respuesta es exitosa
        if ($userResponse->failed()) {
            return response()->json(['error' => 'Usuario no autorizado o no encontrado'], 401);
        }


        // 5. Si el usuario existe, retornar la lista de eventos
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
        // 1. Obtener el ID del usuario desde el encabezado
        $userId = $request->header('X-User-ID');

        if (!$userId) {
            return response()->json(['error' => 'Falta el encabezado X-User-ID'], 400);
        }

        // 2. Verificar el usuario con el microservicio
        $userResponse = Http::get("http://localhost:8001/api/users/$userId");

        if ($userResponse->failed()) {
            return response()->json(['error' => 'Usuario no autorizado o no encontrado'], 401);
        }

        $userData = $userResponse->json();
        $user = $userData['user'] ?? null;


        // 3. Verificar si el rol es "admin"
        if (!isset($userData['user']['role']) || $userData['user']['role'] !== 'admin') {
            return response()->json(['error' => 'Acceso denegado. Solo administradores pueden crear eventos.'], 403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'date' => 'required|date',
            'location' => 'required|string|max:255',
            'capacity' => 'required|integer|min:1',
        ]);
        

        // 4. Crear evento normalmente
        $event = Event::create($request->all());

        return response()->json($event, 201);
    }


    // Actualizar un evento
    public function update(Request $request, $id)
    {
        // 1. Obtener el ID del usuario desde el encabezado
        $userId = $request->header('X-User-ID');

        if (!$userId) {
            return response()->json(['error' => 'Falta el encabezado X-User-ID'], 400);
        }

        // 2. Consultar el usuario en el user-service
        $userResponse = Http::get("http://localhost:8001/api/users/$userId");

        if ($userResponse->failed()) {
            return response()->json(['error' => 'Usuario no autorizado o no encontrado'], 401);
        }

        $userData = $userResponse->json();
        $user = $userData['user'] ?? null;

        // 3. Verificar que el rol sea admin
        if (!isset($user['role']) || $user['role'] !== 'admin') {
            return response()->json(['error' => 'Acceso denegado. Solo administradores pueden actualizar eventos.'], 403);
        }

        // 4. Buscar el evento
        $event = Event::find($id);

        if (!$event) {
            return response()->json(['error' => 'Evento no encontrado'], 404);
        }

        // 5. Validar datos y actualizar
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'date' => 'sometimes|required|date',
            'location' => 'sometimes|required|string|max:255',
            'capacity' => 'sometimes|required|integer|min:1',
        ]);

        $event->update($validated);

        return response()->json($event);
    }

    // Eliminar un evento
    public function destroy(Request $request, $id)
    {
        // 1. Obtener el ID del usuario desde el encabezado
        $userId = $request->header('X-User-ID');
    
        if (!$userId) {
            return response()->json(['error' => 'Falta el encabezado X-User-ID'], 400);
        }
    
        // 2. Consultar el usuario en el user-service
        $userResponse = Http::get("http://localhost:8001/api/users/$userId");
    
        if ($userResponse->failed()) {
            return response()->json(['error' => 'Usuario no autorizado o no encontrado'], 401);
        }
    
        $userData = $userResponse->json();
        $user = $userData['user'] ?? null;
    
        // 3. Verificar si el usuario es admin
        if (!isset($user['role']) || $user['role'] !== 'admin') {
            return response()->json(['error' => 'Acceso denegado. Solo administradores pueden eliminar eventos.'], 403);
        }
    
        // 4. Buscar el evento
        $event = Event::find($id);
    
        if (!$event) {
            return response()->json(['error' => 'Evento no encontrado'], 404);
        }
    
        // 5. Eliminar el evento
        $event->delete();
    
        return response()->json(['message' => 'Evento eliminado correctamente']);
    }
}

