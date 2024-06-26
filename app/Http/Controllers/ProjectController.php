<?php

namespace App\Http\Controllers;

use App\Models\Type;
use Illuminate\Http\Request;
use App\Models\Project;
use Illuminate\Support\Facades\Auth;



class ProjectController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'web-site' => 'required|url',
            'slug' => 'required|string|unique:projects,slug',
            'description' => 'required|string',
        ]);

        // Nuovo progetto.
        $project = new Project();
        $project->name = $request->name;
        $project->description = $request->description;
        $project->web_site = $request->input('web-site');
        $project->slug = $request->slug;
        $project->user_id = auth()->user()->id;
        $project->save();

        // Vengo reindirizzato a projects.index. Messaggio: 'Progetto Creato'.
        return redirect()->route('projects.index')->with('success', 'Progetto creato.');
    }

    public function index()
    {
        // Recupera l'ID dell'utente autenticato
        $userId = Auth::id();

        // Recupera i progetti dell'utente autenticato
        $projects = Project::where('user_id', $userId)->get();

        // Mostra la vista con i progetti
        return view('projects.index', compact('projects'));
    }

    public function show(Project $project)
{
    return view('projects.show', compact('project'));
}
    public function create()
    {
        $types = Type::all(); // Recupera tutti i tipi di progetto
        return view('projects.create', compact('types'));
    }


    public function update(Request $request, Project $project)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'web-site' => 'nullable|url',
            'slug' => 'required|string|unique:projects,slug,' . $project->id,
            'description' => 'required|string',
        ]);

        // Qui invece lo aggiorno. 
        $project->name = $request->name;
        $project->web_site = $request->input('web-site'); // Campo web-site
        $project->slug = $request->slug;
        $project->description = $request->description;
        $project->save();

        // Vengo reindirizzato. Compare un altro messaggio.  
        return redirect()->route('projects.index')->with('success', 'Progetto aggiornato.');
    }

    public function destroy(Project $project)
    {
        // L'utente è loggato?
        if (auth()->user()->id !== $project->user_id) {
            return abort(403); // Accesso negato
        }

        // Elimina il progetto
        $project->delete();

        // Reindirizzamento con terzo messaggio.
        return redirect()->route('projects.index')->with('success', 'Progetto eliminato.');
    }
}
