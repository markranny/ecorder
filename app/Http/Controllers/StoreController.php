<?php

namespace App\Http\Controllers;
use App\Models\rbostoretables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Carbon\Carbon;
use Illuminate\Validation\ValidationException;

class StoreController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $excludedNames = ['HQ', 'DEMO', 'DISPATCH', 'FINISH GOODS'];
            $rbostoretables = rbostoretables::select([
                'STOREID',
                'NAME',
                'ROUTES',
                'TYPES',
                'BLOCKED',
                'CUTOFFTIME',
            ])
            ->orderBy('NAME', 'asc')
            ->whereNotIn('NAME', $excludedNames) 
            ->get()
            ->map(function ($store) {
                // Format CUTOFFTIME for display in the frontend if it's a datetime
                if ($store->CUTOFFTIME && strlen($store->CUTOFFTIME) > 8) {
                    $store->CUTOFFTIME = Carbon::parse($store->CUTOFFTIME)->format('H:i');
                }
                return $store;
            });

        return Inertia::render('Storetable/Index', ['rbostoretables' => $rbostoretables]);
    }

    public function create()
    {
        
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'storeid' => 'required|string|unique:rbostoretables,STOREID',
                'name'=> 'required|string',
                'cutofftime' => 'nullable|string',
            ]);

            $utcDateTime = Carbon::now('UTC');
            $currentDateTime = $utcDateTime->setTimezone('Asia/Shanghai');
            
            // Format the cutofftime as a proper datetime for MySQL
            $cutoffTime = $request->cutofftime ?? '16:00';
            $formattedCutoffTime = $cutoffTime;
            
            // If the database column is DATETIME type, add the date part
            if (DB::getSchemaBuilder()->getColumnType('rbostoretables', 'CUTOFFTIME') === 'datetime') {
                $formattedCutoffTime = date('Y-m-d') . ' ' . $cutoffTime . ':00';
            }

            rbostoretables::create([
                'STOREID' => $request->storeid,
                'NAME' => $request->name,
                'ADDRESS' => 'N/A',
                'STREET' => 'N/A',
                'ZIPCODE' => 'N/A',
                'CITY' => 'N/A',
                'STATE' => 'N/A',
                'COUNTRY' => 'N/A',
                'PHONE' => 'N/A',
                'CURRENCY' => 'N/A',
                'SQLSERVERNAME' => 'N/A',
                'DATABASENAME' => 'N/A',
                'USERNAME' => 'N/A',
                'PASSWORD' => 'N/A',
                'WINDOWSAUTHENTICATION' => '1',
                'FORMINFOFIELD1' => 'N/A',
                'FORMINFOFIELD2' => 'N/A',
                'FORMINFOFIELD3' => 'N/A',
                'FORMINFOFIELD4' => 'N/A',
                'BLOCKED' => '0',
                'TYPES' => $request->types ?? 'NONE',
                'CUTOFFTIME' => $formattedCutoffTime,
            ]);

            return redirect()->route('store.index')
            ->with('message', 'Store Created Successfully')
            ->with('isSuccess', true);
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())
            ->withInput()
            ->with('message',$e->errors())
            ->with('isSuccess', false);
        } catch (\Exception $e) {
            return back()
            ->withInput()
            ->with('message', 'Error: ' . $e->getMessage())
            ->with('isSuccess', false);
        }
    }

    public function show(string $id)
    {
        
    }

    public function edit(string $id)
    {
        
    }

    public function update(Request $request, string $id)
    {
        try {
            $request->validate([
                'NAME'=> 'required|string',
                'CUTOFFTIME' => 'nullable|string',
            ]);
            
            // Format the cutofftime as a proper datetime for MySQL
            $cutoffTime = $request->CUTOFFTIME;
            $formattedCutoffTime = $cutoffTime;
            
            // If the database column is DATETIME type, add the date part
            if (DB::getSchemaBuilder()->getColumnType('rbostoretables', 'CUTOFFTIME') === 'datetime') {
                $formattedCutoffTime = date('Y-m-d') . ' ' . $cutoffTime . ':00';
            }
            
            rbostoretables::where('STOREID',$request->STOREID)->
            update([
                'NAME'=> $request->NAME,
                'ROUTES'=> $request->ROUTES,
                'TYPES'=> $request->TYPES,
                'BLOCKED'=> $request->BLOCKED,
                'CUTOFFTIME' => $formattedCutoffTime,
            ]);

            return redirect()->route('store.index')
            ->with('message', 'Store Updated Successfully')
            ->with('isSuccess', true);
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())
            ->withInput()
            ->with('message', $e->errors())
            ->with('isSuccess', false);
        } catch (\Exception $e) {
            return back()
            ->withInput()
            ->with('message', 'Error: ' . $e->getMessage())
            ->with('isSuccess', false);
        }
    }

    public function destroy(string $id)
    {
        //
    }
}