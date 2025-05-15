<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;

class PortfolioController extends Controller
{
    //
    /**
     * Muestra una lista de todos los proyectos.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $projects = Project::with('skills')->get();
        return view('portfolio', compact('projects'));
    }
}
