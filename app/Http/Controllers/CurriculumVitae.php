<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Providers\NotionService;
use FiveamCode\LaravelNotionApi\Notion;

class CurriculumVitae extends Controller
{
    protected $notionInstance;
    protected $internalNotion;
    protected $blockIds;
    // ----------------------------------------------------------- Constructor
    public function __construct(NotionService $notionService) {
        $this->notionInstance = new Notion(env('NOTION_API_TOKEN'));
        $this->internalNotion = $notionService;
        $this->blockIds = [
            'mainPage' => env('NOTION_CURRICULUM_VITAE_ID'),
            'principal' => 'be694f82-eff2-4dac-80aa-537a518712f2', // Este es blocke interno principal, contiene los dos columnas
            'experienciaLaboral' => '35fe5f49-98a1-4404-878c-e4d4751c8ab4', // Este es de la data base de los trabajos que he hecho
        ];
    }


    // ---------------------------------- Para tomar la información general de las dos columnas
    public function index()
    {
        $notionInstance =$this->notionInstance;
        $tempBlockId = $this->blockIds;
        $nQuery = $notionInstance->block($tempBlockId['principal'])->children()->withUnsupported()->asCollection();
        return $nQuery;
    }

    // ---------------------------------- Internal es el servicio propio, de ser necesario, sin embargo es responsable no usarlo
    public function internalIndex()
    {
        $notionInstance =$this->internalNotion;
        $nQuery = $notionInstance->getPortfolio();
        return $nQuery;
    }

    // -------------------------------------------------------- Retorna los hijos de un bloque como colección
    public function retriveBlockChildren($id) {
        return  $this->notionInstance->block($id)->children()->withUnsupported()->asCollection();
    }

    // -------------------------------------------------------- Retorna los hijos de un bloque como colección
    public function retriveBlock($id) {
        return  $this->notionInstance->block($id)->retrieve();
    }

    // -------------------------------------------------------- Retorna el page
    public function retrivePage($id) {
        return  $this->notionInstance->pages()->find($id);
    }

    // ------------------------------------------------- Retorna Todas las paginas de una data base
    public function retriveDatabase($id) {
        return $this->notionInstance->database($id)->query()->asCollection();
    }

    public function getTitle() {
        $tempBlockId = $this->blockIds;
        $mainpage = $this->retrivePage($tempBlockId['mainPage']);
        return $mainpage->getTitle();
    }

    // ------------------------------------------------------------------- Render de la web
    public function show() {
        $mainpage = $this->getTitle();
        $info = $this->index();
        $childrenInfo = '';
        $tempBlockId = $this->blockIds;

        $laborInfo = $this->retriveDatabase($tempBlockId['experienciaLaboral']);
        try {
            $childrenInfo = $this->retriveBlockChildren($info[1]->getId());
            //code...
        } catch (\Throwable $th) {
            //throw $th;
        }
        return view('home')->with('notionInfo', $info)
        ->with('childrenInfo', $childrenInfo)
        ->with('laborInfo', $laborInfo)
        ->with('title', $mainpage);
    }
}
