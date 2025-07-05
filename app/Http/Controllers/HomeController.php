<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\announcements;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Illuminate\Support\Facades\Agent;
use Detection\MobileDetect;

class HomeController extends Controller
{
    public function index(){
        $announcements = Announcements::select('*')->get();
        $detect = new MobileDetect;
    
        if ($detect->isMobile()) {
            return Inertia::render('Home/admin', ['announcements' => $announcements]);
        } else {
            return Inertia::render('Home/admin', ['announcements' => $announcements]);
        }
    }

    public function admin() {
        $announcements = Announcements::select('*')->get();
        $detect = new MobileDetect;
    
        if ($detect->isMobile()) {
            return Inertia::render('Home/admin', ['announcements' => $announcements]);
        } else {
            return Inertia::render('Home/admin', ['announcements' => $announcements]);
        }
        
    }

    public function downloadFile($id)
    {
        /* dd('test'); */
        $announcement = Announcements::findOrFail($id);
        
        if (!$announcement->file_path) {
            /* abort(404, 'File not found'); */
            return redirect()->back()
                    ->with('message', 'File not found!')
                    ->with('isError', true);
        }

        $path = storage_path('app/public/' . $announcement->file_path);

        if (!file_exists($path)) {
            /* abort(404, 'File not found'); */
            return redirect()->back()
                    ->with('message', 'File not exist!')
                    ->with('isError', true);
        }

        return response()->download($path);
    }

    public function offline(){
        return Inertia::render('Offline');
    }


    
   
}
