<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

// IMPORTANT: Import the models you want to count
use App\Models\Eleve;       // Assuming you have an Eleve model
use App\Models\Enseignant;  // Assuming you have an Enseignant model
use App\Models\PersonnelDirection;   // You already have this one
use App\Models\Groupe;      // Assuming you have a Groupe model

class DashboardController extends Controller
{
    public function index()
    {
        // 1. GATHER DATA
        // Use the count() method for efficiency. It runs a `SELECT COUNT(*)` query.
        $totalEleves = Eleve::count();
        $totalEnseignants = Enseignant::count();
        $totalPersonnels = PersonnelDirection::count();
        $totalGroupes = Groupe::count();
        $recentEleves = Eleve::latest()->take(3)->get();
        $niveaux = Groupe::distinct()->pluck('niveau')->toArray();
         $niveauData = [];
        foreach ($niveaux as $niveau) {
        $groupeIds = Groupe::where('niveau', $niveau)->pluck('id');
        $count = Eleve::whereIn('groupe_id', $groupeIds)->count();
        $niveauData[] = $count;
        }
        $niveauLabels = $niveaux;


        // 2. PASS DATA TO THE VIEW
        // The 'compact' function is a clean way to create an array of these variables.
        return view('admin.dashboard.index', compact(
            'totalEleves',
            'totalEnseignants',
            'totalPersonnels',
            'totalGroupes',
            'recentEleves',
            'niveauLabels',
            'niveauData'
        ));
    }
}