<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\posperiodicdiscounts;
use App\Models\posperiodicdiscountlines;
use App\Models\inventtables;
use App\Models\rboinventtables;
use App\Models\inventtablemodules;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

class POSdiscountlinesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $posperiodicdiscountlines = posperiodicdiscountlines::select([
            'OFFERID',
            'LINEID',
            'PRODUCTTYPE',
            'ID',
            'DEALPRICEORDISCPCT',
            'LINEGROUP',
            'DISCTYPE',
        ])
        ->get();

        return Inertia::render('Posperiodicdiscountlines/index', ['posperiodicdiscountlines' => $posperiodicdiscountlines]);
    }

    public function MNM($offerid, $discountType)
    {
        $items = DB::table('inventtablemodules as a')
          ->select(
              'a.ITEMID as itemid',
              'b.itemname as itemname',
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

        Inertia::share('items', $items);

        $posperiodicdiscountlines = Posperiodicdiscountlines::where('offerid', $offerid)->get();
        return Inertia::render('Posperiodicdiscountlines/index', [
        'posperiodicdiscountlines' => $posperiodicdiscountlines,
        'offerid' => $offerid,
        'discountType' => $discountType,
        'items' => $items,
        
    ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'OFFERID'=> 'required|string',
                'LINEID'=> 'required|integer',
                'PRODUCTTYPE'=> 'required|integer',
                'ID'=> 'required|string',
                'DEALPRICEORDISCPCT'=> 'required|integer',
                'LINEGROUP'=> 'required|string',
                'DISCTYPE'=> 'required|integer',       
            ]);


            posperiodicdiscountlines::create([
                
                'OFFERID'=> $request->OFFERID,
                'LINEID'=> $request->LINEID,
                'PRODUCTTYPE'=> $request->PRODUCTTYPE,
                'ID'=> $request->ID,
                'DEALPRICEORDISCPCT'=> $request->DEALPRICEORDISCPCT,
                'LINEGROUP'=> $request->LINEGROUP,
                'DISCTYPE'=> $request->DISCTYPE,                      
            ]);


            $offerid = $request->OFFERID;
            return redirect()->back()
            ->with('message', 'POSdiscount created successfully')
            ->with('isSuccess', true);

        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())
            ->withInput()
            ->with('message',$e->errors())
            ->with('isSuccess', false);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $request->validate([
                'OFFERID'=> 'required|string',
                'LINEID'=> 'required|integer',
                'PRODUCTTYPE'=> 'required|integer',
                'ID'=> 'required|string',
                'DEALPRICEORDISCPCT'=> 'required|integer',
                'LINEGROUP'=> 'required|string',
                'DISCTYPE'=> 'required|integer',       
            ]);

            posperiodicdiscountlines::where('OFFERID',$request->OFFERID)->
            update([
                'LINEID'=> $request->LINEID,
                'PRODUCTTYPE'=> $request->PRODUCTTYPE,
                'ID'=> $request->ID,
                'DEALPRICEORDISCPCT'=> $request->DEALPRICEORDISCPCT,
                'LINEGROUP'=> $request->LINEGROUP,
                'DISCTYPE'=> $request->DISCTYPE,                     
            ]);


            return redirect()->route('posperiodicdiscountlines.index')
            ->with('message', 'POSdiscount updated successfully')
            ->with('isSuccess', true);
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())
            ->withInput()
            ->with('message', $e->errors())
            ->with('isSuccess', false);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id, Request $request)
    {

        /* dd($request->LINEID); */
        try {
            $request->validate([
                'LINEID' => 'required|exists:posperiodicdiscountlines,LINEID',
            ]);

            posperiodicdiscountlines::where('LINEID', $request->LINEID)->delete();

            /* return redirect()->route('posperiodicdiscountlines.index')
            ->with('message', 'POSdiscount deleted successfully')
            ->with('isSuccess', true); */
            $offerid = $request->OFFERID;
            return redirect()->back()
            ->with('message', 'POSdiscount deleted successfully')
            ->with('isSuccess', true);
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())
            ->withInput()
            ->with('message', $e->errors())
            ->with('isSuccess', false);
        }
    }
}
