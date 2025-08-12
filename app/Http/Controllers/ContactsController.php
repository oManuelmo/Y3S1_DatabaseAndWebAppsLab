<?php

namespace App\Http\Controllers;

class ContactsController extends Controller
{
    public function index()
    {
        return view('pages.contacts');
    }
}
