<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\posperiodicdiscounts;
use App\Models\posdiscvalidationperiods;
use illuminate\Validation\ValidationException;
use Inertia\Inertia;


class POSperiodicdiscountController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $posperiodicdiscounts = posperiodicdiscounts::select([
            'OFFERID',
            'DESCRIPTION',
            DB::raw("CASE WHEN STATUS = 1 THEN 'Enabled' ELSE 'Disabled' END as STATUS"),
            'DISCVALIDPERIODID',
            DB::raw("CASE 
            WHEN DISCOUNTTYPE = 0 THEN 'DealPrice' 
            WHEN DISCOUNTTYPE = 1 THEN 'DiscountPercent'
            WHEN DISCOUNTTYPE = 2 THEN 'DiscountAmount'
            WHEN DISCOUNTTYPE = 3 THEN 'LineSpecific'
            ELSE 'Disabled' 
            END as DISCOUNTTYPE"),
            'DEALPRICEVALUE',
            'DISCOUNTPCTVALUE',
            'DISCOUNTAMOUNTVALUE',
            ])
        ->get();

        $posperiodicdiscounts->transform(function ($item) {
            $item->DISCOUNTAMOUNTVALUE = number_format($item->DISCOUNTAMOUNTVALUE, 2); 
            return $item;
        });

        $posperiodicdiscounts->transform(function ($item) {
            $item->DISCOUNTPCTVALUE = number_format($item->DISCOUNTPCTVALUE, 2); 
            return $item;
        });

        $posperiodicdiscounts->transform(function ($item) {
            $item->DEALPRICEVALUE = number_format($item->DEALPRICEVALUE, 2); 
            return $item;
        });

        $DISCVALIDPERIODID = posdiscvalidationperiods::select('ID','DESCRIPTION')->get();

        /* dd($posperiodicdiscounts); */


        return Inertia::render('Posdiscounts/index', [
            'posperiodicdiscounts' => $posperiodicdiscounts,
            'DISCVALIDPERIODID' => $DISCVALIDPERIODID,
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
                /* 'OFFERID'=> 'required|string', */
                'DESCRIPTION'=> 'required|string',
                'STATUS'=> 'required|integer',
                /* 'PDTYPE'=> 'required|integer', */
                /* 'PRIORITY'=> 'required|integer', */
                /* 'DISCVALIDPERIODID'=> 'required|string', */
                'DISCOUNTTYPE'=> 'required|integer',
                /* 'NOOFLINESTOTRIGGER'=> 'required|integer', */
                /* 'DEALPRICEVALUE'=> 'required|integer', */
                /* 'DISCOUNTPCTVALUE'=> 'required|integer', */
                /* 'DISCOUNTAMOUNTVALUE'=> 'required|integer', */
                /* 'PRICEGROUP'=> 'required|string',   */ 
                /* 'TRIGGERED'=> 'required|integer',  */   
            ]);
            
            /* $offeridcount = $request->OFFERID; */

            /* dd($request->DISCOUNTTYPE); */

            /* $offeridcount = posperiodicdiscounts::count();

            if ($offeridcount === 0) {
                $offeridseries = 1; 
            } else {
                $offeridseries = $offeridcount + 1;
            } */

            $idcount = posperiodicdiscounts::orderBy('OFFERID', 'desc')->value('OFFERID');

            if ($idcount === 0) {
                $idseries = 1; 
            } else {
                $idseries = $idcount + 1;
            }

            
            posperiodicdiscounts::create([
                
                'OFFERID'=> $idseries,
                'DESCRIPTION'=> $request->DESCRIPTION,
                'STATUS'=> $request->STATUS,
                'DISCVALIDPERIODID'=> $request->DISCVALIDPERIODID,
                'DISCOUNTTYPE'=> $request->DISCOUNTTYPE,      
                'DEALPRICEVALUE'=> $request->DEALPRICEVALUE, 
                'DISCOUNTPCTVALUE'=> $request->DISCOUNTPCTVALUE, 
                'DISCOUNTAMOUNTVALUE'=> $request->DISCOUNTAMOUNTVALUE,               
            ]);


            return redirect()->route('posperiodicdiscounts.index')
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
                'DESCRIPTION'=> 'required|string',
                'STATUS'=> 'required|integer',
                /* 'PDTYPE'=> 'required|integer',
                'PRIORITY'=> 'required|integer', */
                'DISCVALIDPERIODID'=> 'required|string',
                'DISCOUNTTYPE'=> 'required|integer',
                /* 'NOOFLINESTOTRIGGER'=> 'required|integer',
                'DEALPRICEVALUE'=> 'required|integer',
                'DISCOUNTPCTVALUE'=> 'required|integer',
                'DISCOUNTAMOUNTVALUE'=> 'required|integer',
                'PRICEGROUP'=> 'required|string',   */     
            ]);

            posperiodicdiscounts::where('OFFERID',$request->OFFERID)->
            update([
                'DESCRIPTION'=> $request->DESCRIPTION,
                'STATUS'=> $request->STATUS,
                /* 'PDTYPE'=> $request->PDTYPE,
                'PRIORITY'=> $request->PRIORITY, */
                'DISCVALIDPERIODID'=> $request->DISCVALIDPERIODID,
                'DISCOUNTTYPE'=> $request->DISCOUNTTYPE,
                /* 'NOOFLINESTOTRIGGER'=> $request->NOOFLINESTOTRIGGER, */
                'DEALPRICEVALUE'=> $request->DEALPRICEVALUE,
                'DISCOUNTPCTVALUE'=> $request->DISCOUNTPCTVALUE,
                'DISCOUNTAMOUNTVALUE'=> $request->DISCOUNTAMOUNTVALUE,
                /* 'PRICEGROUP'=> $request->PRICEGROUP,    */                  
            ]);


            return redirect()->route('posperiodicdiscounts.index')
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
    public function destroy(string $OFFERID, Request $request)
    {
       
        try {
            $request->validate([
                'OFFERID' => 'required|exists:posperiodicdiscounts,OFFERID',
            ]);

            posperiodicdiscounts::where('OFFERID', $request->OFFERID)->delete();

            return redirect()->route('posperiodicdiscounts.index')
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
