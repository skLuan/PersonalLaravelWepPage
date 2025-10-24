<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

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
        $sendeData = compact('projects');
        $sendeData['locale'] = app()->getLocale();
        App::setlocale(app()->getLocale());
        return view('portfolio', $sendeData);
    }
}
