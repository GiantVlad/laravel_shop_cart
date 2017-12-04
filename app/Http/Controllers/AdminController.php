<?php
/**
 * Created by PhpStorm.
 * User: ale
 * Date: 01.12.2017
 * Time: 19:15
 */

namespace App\Http\Controllers;


class AdminController extends Controller
{
    public function __construct ()
    {
        $this->middleware('auth:admin');
    }

    public function index()
    {
        return view('admin.dashboard');
    }
}