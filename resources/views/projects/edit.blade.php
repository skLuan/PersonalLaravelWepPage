@extends('layouts.projects') <!-- Asegúrate de tener un layout base -->

@section('content')
    <div class="container mx-auto p-4">
        <h1 class="text-2xl font-bold mb-4">Editar Proyecto: {{ $project->title }}</h1>

        @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 p-4 mb-4 rounded">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if (session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 p-4 mb-4 rounded">
                {{ session('success') }}
            </div>
        @endif

        <form action="{{ route('projects.update', $project->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="mb-4">
                <label for="title" class="block text-sm font-medium text-gray-700">Título</label>
                <input type="text" name="title" id="title" value="{{ old('title', $project->title) }}"
                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                       required>
            </div>

            <div class="mb-4">
                <label for="short_description" class="block text-sm font-medium text-gray-700">Descripción Corta</label>
                <textarea name="short_description" id="short_description" rows="3"
                          class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                          required>{{ old('short_description', $project->short_description) }}</textarea>
            </div>

            <div class="mb-4">
                <label for="url_img" class="block text-sm font-medium text-gray-700">URL de la Imagen</label>
                <input type="url" name="url_img" id="url_img" value="{{ old('url_img', $project->url_img) }}"
                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                       required>
            </div>

            <div class="mb-4">
                <label for="site_url" class="block text-sm font-medium text-gray-700">URL del Sitio</label>
                <input type="url" name="site_url" id="site_url" value="{{ old('site_url', $project->site_url) }}"
                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
            </div>

            <div class="mb-4">
                <label for="body" class="block text-sm font-medium text-gray-700">Contenido</label>
                <textarea name="body" id="body" rows="6"
                          class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                          required>{{ old('body', $project->body) }}</textarea>
                <p class="text-xs text-gray-500 mt-1">Usa Markdown o HTML para el contenido detallado.</p>
            </div>

            <div class="mb-4">
                <label for="skills" class="block text-sm font-medium text-gray-700">Habilidades</label>
                <select name="skills[]" id="skills" multiple
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                        required>
                    @foreach ($skills as $skill)
                        <option value="{{ $skill->id }}"
                                {{ in_array($skill->id, old('skills', $project->skills->pluck('id')->toArray())) ? 'selected' : '' }}>
                            {{ $skill->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">
                Actualizar Proyecto
            </button>
            <a href="{{ route('projects.index') }}" class="ml-4 text-gray-600 hover:text-gray-800">Cancelar</a>
        </form>
    </div>
@endsection