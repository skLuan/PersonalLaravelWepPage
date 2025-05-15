@extends('layouts.app') <!-- Asegúrate de tener un layout base -->

@section('content')
    <div class="container mx-auto p-4">
        <h1 class="text-2xl font-bold mb-4">Mis Proyectos</h1>

        @if (session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 p-4 mb-4 rounded">
                {{ session('success') }}
            </div>
        @endif

        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
            @forelse ($projects as $project)
                <div class="project-card bg-gray-900 border border-purple-900 rounded-lg overflow-hidden shadow-lg">
                    <img src="{{ $project->image_path }}" alt="{{ $project->title }}" class="w-full h-48 object-cover">
                    <div class="p-4">
                        <h3 class="text-xl font-semibold text-yellow-400 mb-2">{{ $project->title }}</h3>
                        <p class="text-gray-300 mb-2"><strong>URL:</strong> <a href="{{ $project->site_url }}" target="_blank" class="text-red-400 hover:underline">{{ $project->site_url }}</a></p>
                        <p class="text-gray-400 mb-2"><strong>Habilidades:</strong>
                            @foreach ($project->skills as $skill)
                                <span class="inline-block bg-purple-800 text-white px-2 py-1 rounded-full text-sm mr-1">{{ $skill->name }}</span>
                            @endforeach
                        </p>
                        <p class="text-gray-400 mb-2">{{ $project->short_description }}</p>
                        <div class="flex space-x-2">
                            <a href="{{ route('projects.show', $project->slug) }}" class="text-indigo-400 hover:underline">Ver más</a>
                            <a href="{{ route('projects.edit', $project->slug) }}" class="text-yellow-400 hover:underline">Editar</a>
                        </div>
                    </div>
                </div>
            @empty
                <p class="text-gray-400">No hay proyectos disponibles.</p>
            @endforelse
        </div>

        <div class="mt-6">
            <a href="{{ route('projects.create') }}" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">Crear Nuevo Proyecto</a>
        </div>
    </div>
@endsection