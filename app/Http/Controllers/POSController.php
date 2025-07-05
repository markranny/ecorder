<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\inventtables;
use App\Models\rboinventtables;
use App\Models\inventtablemodules;
use App\Models\importproducts;
use App\Models\rboinventitemretailgroups;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

class POSController extends Controller
{
    public function index(Request $request)
    {

        $category = DB::table('ecpos_tailwind.rboinventitemretailgroups')
            ->select('groupid','name')   
            ->get();


        /* dd($category); */

        $items = DB::table('inventtablemodules as a')
          ->select(
              'a.ITEMID as itemid',
              'b.itemname as itemname',
              /* 'a.quantity as quantity', */
              DB::raw('CAST(a.quantity as int) as quantity'),
              'c.itemgroup as itemgroup',
              'c.itemdepartment as specialgroup',
              DB::raw('ROUND(FORMAT(a.priceincltax, "N2"), 2) as price'),
              DB::raw('ROUND(FORMAT(a.price, "N2"), 2) as cost'),
              DB::raw("CASE WHEN d.ITEMBARCODE <> '' THEN d.itembarcode ELSE 'N/A' END as barcode")
          )
          ->leftJoin('inventtables as b', 'a.ITEMID', '=', 'b.itemid')
          ->leftJoin('rboinventtables as c', 'b.itemid', '=', 'c.itemid')
          ->leftJoin('inventitembarcodes as d', 'c.barcode', '=', 'd.ITEMBARCODE')
          ->get();

        return Inertia::render('Menu/Index', ['items' => $items, 'category' => $category]);
    }
}
