<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Skill;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    /**
     * Muestra una lista de todos los proyectos.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $projects = Project::with('skills')->get();
        return view('projects.index', compact('projects'));
    }

    /**
     * Muestra el formulario para crear un nuevo proyecto.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $skills = Skill::all();
        return view('projects.create', compact('skills'));
    }

    /**
     * Almacena un nuevo proyecto en la base de datos.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'short_description' => 'required|string',
            'image_path' => 'required|url',
            'site_url' => 'nullable|url',
            'body' => 'required|string',
            'skills' => 'required|array',
            'skills.*' => 'exists:skills,id',
        ]);

        $project = Project::create([
            'title' => $validated['title'],
            'slug' => \Illuminate\Support\Str::slug($validated['title']),
            'short_description' => $validated['short_description'],
            'image_path' => $validated['image_path'],
            'site_url' => $validated['site_url'],
            'body' => $validated['body'],
        ]);

        $project->skills()->sync($request->skills);

        return redirect()->route('projects.index')->with('success', 'Proyecto creado exitosamente.');
    }

    /**
     * Muestra los detalles de un proyecto específico.
     *
     * @param  \App\Models\Project  $project
     * @return \Illuminate\Http\Response
     */
    public function show(Project $project)
    {
        return view('projects.show', compact('project'));
    }

    /**
     * Muestra el formulario para editar un proyecto existente.
     *
     * @param  \App\Models\Project  $project
     * @return \Illuminate\Http\Response
     */
    public function edit(Project $project)
    {
        $skills = Skill::all();
        return view('projects.edit', compact('project', 'skills'));
    }

    /**
     * Actualiza un proyecto existente en la base de datos.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Project  $project
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Project $project)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'short_description' => 'required|string',
            'image_path' => 'required|url',
            'site_url' => 'nullable|url',
            'body' => 'required|string',
            'skills' => 'required|array',
            'skills.*' => 'exists:skills,id',
        ]);

        $project->update([
            'title' => $validated['title'],
            'slug' => \Illuminate\Support\Str::slug($validated['title']),
            'short_description' => $validated['short_description'],
            'image_path' => $validated['image_path'],
            'site_url' => $validated['site_url'],
            'body' => $validated['body'],
        ]);

        $project->skills()->sync($request->skills);

        return redirect()->route('projects.index')->with('success', 'Proyecto actualizado exitosamente.');
    }

    /**
     * Elimina un proyecto de la base de datos.
     *
     * @param  \App\Models\Project  $project
     * @return \Illuminate\Http\Response
     */
    public function destroy(Project $project)
    {
        $project->delete();
        return redirect()->route('projects.index')->with('success', 'Proyecto eliminado exitosamente.');
    }
}
