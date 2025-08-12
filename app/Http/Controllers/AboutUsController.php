<?php

namespace App\Http\Controllers;

class AboutUsController extends Controller
{
    public function index()
    {
        
        $team = [
            [
                'name' => 'Leonardo Ribeiro',
                'id' => 'up202205144',
            ],
            [
                'name' => 'Manuel Mo',
                'id' => 'up202205000',
            ],
            [
                'name' => 'Gonçalo Pinto',
                'id' => 'up202204943',
            ],
            [
                'name' => 'Tomás Sabino',
                'id' => 'up202205152',
            ],
        ];

        $group = 'Group 05 from Class 04';
        $university = 'FEUP, Faculdade de Engenharia da Universidade do Porto';
        $project_note = 'This project was created as part of the course "Laboratório de Bases de Dados e Aplicações Web."';

        return view('pages.about', compact('team', 'group', 'university', 'project_note'));
    }
}

