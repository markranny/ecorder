<?php

namespace App\Http\Controllers;
use App\Models\inventjournaltables;
use App\Models\inventjournaltrans;
use App\Models\inventjournaltransrepos;
use App\Models\numbersequencevalues;
use App\Models\inventtables;
use App\Models\control;
use App\Models\rbostoretables;
use App\Models\rboinventtables;
use App\Models\inventtablemodules;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Google_Client;
use Google_Service_Sheets;
use Inertia\Inertia;
use Carbon\Carbon;

class ItemOrderController extends Controller
{
    public function index()
    {
        $storename = Auth::user()->storeid;

        $inventjournaltrans = DB::table('inventjournaltables as a')
        ->Join('inventjournaltrans as b', 'a.JOURNALID', '=', 'b.JOURNALID')
        ->leftJoin('inventtables as c', 'b.ITEMID', '=', 'c.itemid')
        ->where('a.STOREID', '=', $storename) 
        ->get();
    
        return Inertia::render('ItemOrders/index', ['inventjournaltrans' => $inventjournaltrans]);
    }

    public function ViewOrders($journalid)
    {
            $storename = Auth::user()->storeid;
            $utcDateTime = Carbon::now('UTC');
            $currentDate = $utcDateTime->setTimezone('Asia/Manila')->toDateString();

            $record = DB::table('inventjournaltables')
            ->where('journalid', $journalid)
            ->where('storeid', $storename)
            ->where('posted', 0)
            ->count();

            if ($record <= 0) {
                return redirect()->route('order.index')
                ->with('message', 'This has already been posted. If you want to view your order, please click on the Report module in the left sidebar')
                ->with('isError', true);

            } else {

            $inventjournaltransrepos = DB::table('inventjournaltransrepos as a')
            ->select('a.*', 'b.*', 'c.*')
            ->leftJoin('inventtables as b', 'a.itemid', '=', 'b.itemid')
            ->leftJoin('rboinventtables as c', 'b.itemid', '=', 'c.itemid')
            ->where('a.journalid',$journalid)
            ->where('a.storename', $storename)
            ->where('a.itemdepartment', 'REGULAR PRODUCT')
            ->where('a.counted', '!=', '0')
            ->get();

            return Inertia::render('ItemOrders/index2', [
                'journalid' => $journalid,
                'inventjournaltransrepos' => $inventjournaltransrepos,
            ]);
            }

    }

    public function fgsync()
    {

        try{
            $utcDateTime = Carbon::now('UTC');
            $beijingDateTime = $utcDateTime->setTimezone('Asia/Manila')->toDateString();

            rboinventtables::query()->
            update([
                'TRANSPARENTSTOCKS' => 0,
            ]);

            }catch(ValidationException $e) {
                return redirect()->back()
                    ->with('message', 'INVALID!')
                    ->with('isError', true);
            }  
            
            return redirect()->back();
    }

    public function autopost()
    {

        $utcDateTime = Carbon::now('UTC');
        $currentDateTime = $utcDateTime->setTimezone('Asia/Manila')->toDateString();
        $storename = Auth::user()->storeid;
        $role = Auth::user()->role;
   
        DB::beginTransaction();
        try {

            $affected = DB::insert('
                INSERT INTO inventjournaltrans 
                (JOURNALID, TRANSDATE, ITEMID, ITEMDEPARTMENT, ADJUSTMENT, COSTPRICE, PRICEUNIT, SALESAMOUNT, INVENTONHAND, COUNTED, REASONREFRECID, VARIANTID, POSTED, POSTEDDATETIME, UNITID, updated_at)
                SELECT journalid, ?, itemid, itemdepartment, counted, 0.00, 0.00, 0.00, 0, counted, \'00001\', 0, 0, ?, \'PCS\', updated_at
                FROM inventjournaltransrepos
                WHERE storename = ? AND journalid = ? 
                AND CAST(transdate AS DATE) = ? 
                AND posted = 0 AND sent = 1
                ON DUPLICATE KEY UPDATE
                TRANSDATE = VALUES(TRANSDATE),
                ADJUSTMENT = VALUES(ADJUSTMENT),
                COUNTED = VALUES(COUNTED),
                POSTEDDATETIME = VALUES(POSTEDDATETIME),
                updated_at = VALUES(updated_at)
            ', [$currentDateTime, $currentDateTime, $currentDateTime]);


            }catch(ValidationException $e) {
                return redirect()->back()
                    ->with('message', 'INVALID!')
                    ->with('isError', true);
            }  
        DB::commit();
            
            return redirect()->back();
    }

    private function getSheetData()
    {
        try{
            $utcDateTime = Carbon::now('UTC');
            $manilaDateTime = $utcDateTime->setTimezone('Asia/Manila');
            $formattedDate = $manilaDateTime->format('F j, Y');
            $formattedDate = strtoupper($formattedDate);

            /* $utcDateTime = Carbon::now('UTC');
            $manilaDateTime = $utcDateTime->setTimezone('Asia/Manila');
            $yesterdayDateTime = $manilaDateTime->subDay();
            $formattedDate = $yesterdayDateTime->format('F j, Y');
            $formattedDate = strtoupper($formattedDate); */

            /* dd($formattedDate); */

            $client = new Google_Client();
            $client->setAuthConfig(storage_path('app/finished-goods-9b2565bb6e35.json'));
            $client->addScope(Google_Service_Sheets::SPREADSHEETS_READONLY);

            $service = new Google_Service_Sheets($client);

            $spreadsheetId = '1qPRNb5MK135DogIi7QlunasYYa_hkYEe50wWCB18yWE';
            $range = "{$formattedDate}!A:D";

            $response = $service->spreadsheets_values->get($spreadsheetId, $range);
            $values = $response->getValues();

            return array_slice(array_map(function($row) {
                return [
                    isset($row[0]) ? $row[0] : '',  
                    isset($row[3]) ? $row[3] : ''   
                ];
            }, $values), 2);
        }catch(ValidationException $e) {
            return redirect()->back()
                ->with('message', 'INVALID!')
                ->with('isError', true);
        }
    }


    public function ItemOrders($journalid)
    {
        $storeName = Auth::user()->storeid;
        $currentDate = Carbon::now('Asia/Manila')->toDateString();

        $journalPosted = DB::table('inventjournaltables')
            ->where('journalid', $journalid)
            ->where('storeid', $storeName)
            ->where('posted', 0)
            ->count();

        if ($journalPosted <= 0) {
            return redirect()->route('order.index')
                ->with('message', 'This has already been posted. If you want to view your order, please click on the Report module in the left sidebar')
                ->with('isError', true);
        }

        $inventJournalTransRepos = DB::table('inventjournaltransrepos AS a')
            ->select('a.*', 'b.*', 'c.*')
            ->leftJoin('inventtables AS b', 'a.itemid', '=', 'b.itemid')
            ->leftJoin('rboinventtables AS c', 'b.itemid', '=', 'c.itemid')
            ->where('a.itemdepartment', 'REGULAR PRODUCT')
            ->where('a.journalid', $journalid)
            ->where('a.storename', $storeName)
            ->where('a.status', '=', '0')
            ->get();

        
        $storeType = DB::table('rbostoretables AS a')
            ->leftJoin('users AS b', 'a.name', '=', 'b.storeid')
            ->where('a.name', $storeName)
            ->where('a.types', 'NONE')
            ->count();

        if ($storeType === 1) {
            return Inertia::render('ItemOrders/index3', [
                'journalid' => $journalid,
                'inventjournaltransrepos' => $inventJournalTransRepos,
            ]);
        } else {
            return Inertia::render('ItemOrders/index', [
                'journalid' => $journalid,
                'inventjournaltransrepos' => $inventJournalTransRepos,
            ]);
        }
    }

    
    public function EnableOrder(Request $request)
    {
    \Log::info('Received itemids:', $request->all());

    $request->validate([
        'itemids' => 'required|array',
        'itemids.*' => 'exists:rboinventtables,itemid',
    ]);

    $itemids = $request->itemids;

    $updatedItems = rboinventtables::whereIn('itemid', $itemids)->get();

    foreach ($updatedItems as $item) {
        $item->Activeondelivery = $item->Activeondelivery == 1 ? 0 : 1;
        $item->save();
    }

    $enabledCount = $updatedItems->where('blocked', 1)->count();
    $disabledCount = $updatedItems->where('blocked', 0)->count();

    return response()->json([
        'message' => "Updated successfully. Enabled: $enabledCount, Disabled: $disabledCount items.",
        'enabled' => $enabledCount,
        'disabled' => $disabledCount
    ]);
    }


    public function updateCountedValue(Request $request)
    {
        try {
            \Log::info('updateCountedValue method reached', $request->all());

            $request->validate([
                'journalId' => 'required|string',
                'itemId' => 'required|string',
                'newValue' => 'required|numeric|min:0',
            ]);

            $record = inventjournaltransrepos::where('JOURNALID', $request->journalId)
                ->where('ITEMID', $request->itemId)
                ->first();

            if (!$record) {
                \Log::warning('Record not found', $request->all());
                return response()->json([
                    'success' => false,
                    'message' => 'Record not found',
                ], 404);
            }

            $record->COUNTED = $request->newValue;
            $record->save();

            \Log::info('Record updated successfully', ['record' => $record]);

            return response()->json([
                'success' => true,
                'message' => 'Counted value updated successfully',
            ]);

        } catch (ValidationException $e) {
            \Log::error('Validation failed', ['errors' => $e->errors()]);
            return response()->json([
                'success' => false,
                'message' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Error updating counted value', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while updating the counted value: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function updateAllCountedValues3(Request $request)
    {
        try {
            \Log::info('updateAllCountedValues method reached', $request->all());
            $request->validate([
                'journalId' => 'required|string',
                'updatedValues' => 'required|array',
            ]);

            $journalId = $request->journalId;
            $updatedValues = $request->updatedValues;

            \DB::beginTransaction();

            foreach ($updatedValues as $itemId => $newValue) {
                $record = inventjournaltransrepos::where('JOURNALID', $journalId)
                    ->where('ITEMID', $itemId)
                    ->first();

                if ($record) {
                    $record->COUNTED = $newValue;
                    $record->save();
                } else {
                    \Log::warning("Record not found for ITEMID: $itemId", ['journalId' => $journalId]);
                }
            }

            \DB::commit();

            \Log::info('All records updated successfully');
            return response()->json([
                'success' => true,
                'message' => 'All counted values updated successfully',
            ]);
        } catch (ValidationException $e) {
            \DB::rollBack();
            \Log::error('Validation failed', ['errors' => $e->errors()]);
            return response()->json([
                'success' => false,
                'message' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            \DB::rollBack();
            \Log::error('Error updating counted values', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while updating the counted values: ' . $e->getMessage(),
            ], 500);
        }
    }



    public function updateAllCountedValues(Request $request)
    {
    try {
        \Log::info('updateAllCountedValues method reached', $request->all());
        $request->validate([
            'journalId' => 'required|string',
            'updatedValues' => 'required|array',
        ]);
        $journalId = $request->journalId;
        $updatedValues = $request->updatedValues;
        
        $errors = [];
        $successCount = 0;

        \DB::beginTransaction();
        foreach ($updatedValues as $itemId => $newValue) {
            $record = inventjournaltransrepos::where('JOURNALID', $journalId)
                ->where('ITEMID', $itemId)
                ->first();

            if ($record) {
                if ($newValue == 0 || $newValue >= $record->moq) {
                    $record->COUNTED = $newValue;
                    $record->save();
                    $successCount++;
                } else {
                    $errors[] = "ITEMID: $itemId - Updated value must be equal to or greater than MOQ ({$record->moq}).";
                }
            } else {
                $errors[] = "Record not found for ITEMID: $itemId";
            }
        }
        
        if (empty($errors)) {
            \DB::commit();
            \Log::info("$successCount records updated successfully");
            return response()->json([
                'success' => true,
                'message' => "$successCount counted values updated successfully",
            ]);
        } else {
            \DB::rollBack();
            return response()->json([
                'message' => "ITEMID: $itemId - Updated value must be equal to or greater than MOQ ({$record->moq}).",
            ]);
        }
    } catch (ValidationException $e) {
        \DB::rollBack();
        \Log::error('Validation failed', ['errors' => $e->errors()]);
        return response()->json([
            'success' => false,
            'message' => $e->errors(),
        ], 422);
    } catch (\Exception $e) {
        \DB::rollBack();
        \Log::error('Error updating counted values', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
        return response()->json([
            'message' => "You don't have any changes!",
        ]);
    }
}
    
    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        try {

            $request->validate([
                'itemname'=> 'required|string',  
                'qty'=> 'required|integer',  
            ]);
            
            $utcDateTime = Carbon::now('UTC');
            $currentDateTime = $utcDateTime->setTimezone('Asia/Manila')->toDateString();

            inventjournaltransrepos::create([
                'JOURNALID'=> $request->JOURNALID,
                'LINENUM'=> '',
                'TRANSDATE'=> $currentDateTime,
                'ITEMID'=> $request->itemname,
                'COUNTED'=> $request->qty,    
                'updated_at'=> $currentDateTime,                
            ]);

            $journalid = $request->JOURNALID;
            return redirect()->route('ItemOrders', ['journalid' => $journalid])
            ->with('message', 'Order Created Successfully')
            ->with('isSuccess', true);
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())
            ->withInput()
            ->with('message',$e->errors())
            ->with('isSuccess', false);
        }
    }

    public function show(string $id)
    {
        //
    }

    public function edit(string $id)
    {
        //
    }


    public function update(Request $request, string $id)
    {
            try {

                $request->validate([
                    'JOURNALID' => 'required|string',  
                ]);

                $utcDateTime = Carbon::now('UTC');
                $currentDateTime = $utcDateTime->setTimezone('Asia/Manila')->toDateString();

                $journalid = $request->JOURNALID;

                $record = DB::table('inventjournaltransrepos')
                ->select('JOURNALID')
                ->where('journalid', $journalid)
                ->count();
                

                if ($record >= 1) {
                    return redirect()->route('ItemOrders', ['journalid' => $journalid])
                    ->with('message', 'You have already generated items!')
                    ->with('isError', true);

                } else {

                    if($request->EndDate != null){

                        $storename = Auth::user()->storeid;

                        DB::insert(
                            'INSERT INTO inventjournaltransrepos (JOURNALID, TRANSDATE, ITEMID, COUNTED, STORENAME)
                             SELECT ?, ?, itemid, counted
                             FROM inventjournaltrans
                             WHERE DATE(POSTEDDATETIME) = ? and STORENAME = ?',
                            [$request->JOURNALID, $currentDateTime, $request->EndDate, $storename]
                        );
                    
                        return redirect()->route('ItemOrders', ['journalid' => $request->JOURNALID])
                            ->with('message', 'Generate Item Successfully')
                            ->with('isSuccess', true);   
                        
                    }else{
                        $storename = Auth::user()->storeid;
                        
                        DB::table('inventjournaltransrepos')
                        ->insertUsing(
                            ['JOURNALID', 'TRANSDATE', 'ITEMID', 'COUNTED', 'STORENAME', 'MOQ'],
                            function ($query) use ($request, $currentDateTime, $storename) {
                                $query->select(
                                        DB::raw("'{$request->JOURNALID}' as JOURNALID"),
                                        DB::raw("'{$currentDateTime}' as TRANSDATE"),
                                        'a.itemid as ITEMID',
                                        DB::raw('0 as COUNTED'),
                                        DB::raw("'{$storename}' as STORENAME"),
                                        DB::raw("CASE WHEN b.moq IS NULL THEN 0 ELSE b.moq END")
                                    )
                                    ->from('inventtables as a')
                                    ->leftJoin('rboinventtables as b', 'a.itemid', '=', 'b.itemid')
                                    ->where('b.activeondelivery', '1');
                            }
                        );
        
                        return redirect()->route('ItemOrders', ['journalid' => $journalid])
                        ->with('message', 'Generate Item Successfully')
                        ->with('isSuccess', true);

                        }
                }

                
            } catch (ValidationException $e) {
                return back()->withErrors($e->errors())
                    ->withInput()
                    ->with('message', $e->errors())
                    ->with('isSuccess', false);
            }
    }

    public function getbwproducts(Request $request)
{
    try {
        $request->validate([
            'JOURNALID' => 'required|string',  
        ]);

        $journalid = $request->JOURNALID;

        $record = DB::table('inventjournaltransrepos')
            ->select('JOURNALID')
            ->where('journalid', $journalid)
            ->count();

        $currentDateTime = DB::table('inventjournaltables')
            ->where('journalid', $journalid)
            ->value('POSTEDDATETIME');
        
        if ($record >= 1) {
            return redirect()->route('ItemOrders', ['journalid' => $journalid])
                ->with('message', 'You have already generated items!')
                ->with('isError', true);
        } else {
            if ($request->EndDate != null) {
                $storename = Auth::user()->storeid;

                // Get the next available ID
                $nextId = DB::table('inventjournaltransrepos')->max('id') + 1;

                DB::insert(
                    'INSERT INTO inventjournaltransrepos (id, JOURNALID, TRANSDATE, ITEMID, COUNTED, STORENAME)
                    SELECT ? + ROW_NUMBER() OVER (ORDER BY itemid), ?, ?, itemid, counted, ?
                    FROM inventjournaltrans
                    WHERE DATE(POSTEDDATETIME) = ? AND STORENAME = ?',
                    [$nextId - 1, $request->JOURNALID, $currentDateTime, $storename, $request->EndDate, $storename]
                );
            
                return redirect()->route('ItemOrders', ['journalid' => $request->JOURNALID])
                    ->with('message', 'Generate Item Successfully')
                    ->with('isSuccess', true);   
            } else {
                $storename = Auth::user()->storeid;

                $getseven = DB::table('rbostoretables')
                    ->where('name', 'like', '%PSC%')
                    ->where('name', $storename)
                    ->count();

                $getgcp = DB::table('rbostoretables')
                    ->where('name', 'like', '%GCP%')
                    ->where('name', $storename)
                    ->count();

                // Get the next available ID
                $nextId = DB::table('inventjournaltransrepos')->max('id') + 1;

                if ($getseven >= 1) {
                    // For PSC stores - filter by SVN items
                    $insertData = DB::table('inventtables as a')
                        ->leftJoin('rboinventtables as b', 'a.itemid', '=', 'b.itemid')
                        ->where('b.activeondelivery', '1')
                        ->where('a.itemid', 'like', '%SVN%')
                        ->select('a.itemid', 'b.itemdepartment', 'b.moq')
                        ->get();

                    // Insert each record individually with explicit ID
                    foreach ($insertData as $index => $item) {
                        DB::table('inventjournaltransrepos')->insert([
                            'id' => $nextId + $index,
                            'JOURNALID' => $request->JOURNALID,
                            'ITEMDEPARTMENT' => $item->itemdepartment,
                            'TRANSDATE' => $currentDateTime,
                            'ITEMID' => $item->itemid,
                            'COUNTED' => 0,
                            'STORENAME' => $storename,
                            'MOQ' => $item->moq ?? 0,
                            'STATUS' => 0,
                            'created_at' => now(),
                            'updated_at' => now()
                        ]);
                    }

                } elseif ($getgcp >= 1) {
                    // For GCP stores - filter by GCP items
                    $insertData = DB::table('inventtables as a')
                        ->leftJoin('rboinventtables as b', 'a.itemid', '=', 'b.itemid')
                        ->where('b.activeondelivery', '1')
                        ->where('a.itemid', 'like', '%GCP%')
                        ->select('a.itemid', 'b.itemdepartment', 'b.moq')
                        ->get();

                    // Insert each record individually with explicit ID
                    foreach ($insertData as $index => $item) {
                        DB::table('inventjournaltransrepos')->insert([
                            'id' => $nextId + $index,
                            'JOURNALID' => $request->JOURNALID,
                            'ITEMDEPARTMENT' => $item->itemdepartment,
                            'TRANSDATE' => $currentDateTime,
                            'ITEMID' => $item->itemid,
                            'COUNTED' => 0,
                            'STORENAME' => $storename,
                            'MOQ' => $item->moq ?? 0,
                            'STATUS' => 0,
                            'created_at' => now(),
                            'updated_at' => now()
                        ]);
                    }

                } else {
                    // For regular stores - all active items
                    $insertData = DB::table('inventtables as a')
                        ->leftJoin('rboinventtables as b', 'a.itemid', '=', 'b.itemid')
                        ->where('b.activeondelivery', '1')
                        ->select('a.itemid', 'b.itemdepartment', 'b.moq')
                        ->get();

                    // Insert each record individually with explicit ID
                    foreach ($insertData as $index => $item) {
                        DB::table('inventjournaltransrepos')->insert([
                            'id' => $nextId + $index,
                            'JOURNALID' => $request->JOURNALID,
                            'ITEMDEPARTMENT' => $item->itemdepartment,
                            'TRANSDATE' => $currentDateTime,
                            'ITEMID' => $item->itemid,
                            'COUNTED' => 0,
                            'STORENAME' => $storename,
                            'MOQ' => $item->moq ?? 0,
                            'STATUS' => 0,
                            'created_at' => now(),
                            'updated_at' => now()
                        ]);
                    }
                }
        
                return redirect()->route('ItemOrders', ['journalid' => $journalid])
                    ->with('message', 'Generate Item Successfully')
                    ->with('isSuccess', true);
            }
        }
    } catch (ValidationException $e) {
        return back()->withErrors($e->errors())
            ->withInput()
            ->with('message', $e->errors())
            ->with('isSuccess', false);
    } catch (\Exception $e) {
        \Log::error('Error in getbwproducts: ' . $e->getMessage(), [
            'trace' => $e->getTraceAsString(),
            'journalid' => $request->JOURNALID ?? 'N/A'
        ]);
        
        return back()
            ->with('message', 'Error generating items: ' . $e->getMessage())
            ->with('isError', true);
    }
}

    public function destroy(string $id, Request $request)
    {
        try {
            $request->validate([
                'JOURNALID' => 'required|exists:inventjournaltrans,JOURNALID',
                'LINENUM' => 'required|exists:inventjournaltrans,LINENUM',
            ]);

            inventjournaltrans::where('journalid', $request->JOURNALID)
            ->where('linenum', $request->LINENUM)
            ->delete();

            $journalid = $request->JOURNALID;

            return redirect()->route('ItemOrders', ['journalid' => $journalid])
            ->with('message', 'Order deleted successfully')
            ->with('isSuccess', true);

        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())
            ->withInput()
            ->with('message', $e->errors())
            ->with('isSuccess', false);
        }
    }

    

    public function post(Request $request)
    {
        $utcDateTime = Carbon::now('UTC');
        $currentDateTime = $utcDateTime->setTimezone('Asia/Manila')->toDateString();
        $storename = Auth::user()->storeid;
        $journalid = $request->journalid;
        $role = Auth::user()->role;
   
        DB::beginTransaction();
        try {
            
            $affected = DB::insert('
            INSERT INTO inventjournaltrans 
            (JOURNALID, TRANSDATE, ITEMID, ITEMDEPARTMENT, ADJUSTMENT, COSTPRICE, PRICEUNIT, SALESAMOUNT, INVENTONHAND, COUNTED, REASONREFRECID, VARIANTID, POSTED, POSTEDDATETIME, UNITID, updated_at)
            SELECT ?, transdate, itemid, itemdepartment, counted, 0.00, 0.00, 0.00, 0, counted, \'00001\', 0, 0, transdate, \'PCS\', updated_at
            FROM inventjournaltransrepos
            WHERE storename = ? AND journalid = ?
            ON DUPLICATE KEY UPDATE
            TRANSDATE = VALUES(TRANSDATE),
            ADJUSTMENT = VALUES(ADJUSTMENT),
            COUNTED = VALUES(COUNTED),
            POSTEDDATETIME = VALUES(POSTEDDATETIME),
            updated_at = VALUES(updated_at)
        ', [$journalid, $storename, $journalid]);

            $request->validate([
                'journalid' => 'required|exists:inventjournaltrans,journalid',
            ]);

            $start_time = Carbon::createFromTime(17, 0, 0); 
            $end_time = Carbon::createFromTime(5, 0, 0)->addDay(); 

            $evening_start = Carbon::createFromTime(17, 1, 0);
            $morning_end = Carbon::createFromTime(5, 0, 0)->addDay();

            $morning_start = Carbon::createFromTime(5, 1, 0);
            $evening_end = Carbon::createFromTime(17, 0, 0);
            
            $record = 
            DB::table('inventjournaltables')
            ->where('journalid', $journalid)
            ->where('storeid', $storename)
            ->whereDate('createddatetime', $currentDateTime)
            ->where('posted', 0)
            ->count();

            if ($record <= 0) {
                return redirect()->route('order.index')
                ->with('message', 'Posting all items is not allowed.')
                ->with('isError', true);
            } 

            else {
                inventjournaltables::where('journalid',$request->journalid)
                ->where('journalid',$request->journalid)
                ->whereDate('createddatetime', $currentDateTime)
                ->update([
                    'posted'=> '1',
                ]);

                inventjournaltrans::where('journalid',$request->journalid)
                ->where('journalid',$request->journalid)
                ->whereDate('transdate', $currentDateTime)
                ->update([
                    'VARIANTID'=> '1',
                ]);

                }

            DB::commit();
            return $affected;
                return response()->json([
                    'success' => true,
                    'message' => 'Order posted successfully'
                ]);


        } catch (ValidationException $e) {
            DB::rollBack();
            return back()->withErrors($e->errors())
            ->withInput()
            ->with('message', $e->errors())
            ->with('isSuccess', false);
        }
    }

    
    public function updatefg(Request $request){
        inventjournaltransrepos::where('journalid',$request->journalid)
                ->where('journalid',$request->journalid)
                ->whereDate('transdate', $currentDateTime)
                ->update([
                    'status'=> 1,
                ]);

                $items = DB::table('inventjournaltrans')
                    ->where('JOURNALID', $request->journalid)
                    ->whereDate('POSTEDDATETIME', $currentDateTime)
                    ->select('ITEMID', 'COUNTED')
                    ->get();

                foreach ($items as $item) {
                    DB::table('rboinventtables')
                        ->where('itemid', $item->ITEMID)
                        ->update([
                            'stocks' => DB::raw("stocks - {$item->COUNTED}")
                        ]);
                }
    }

    public function DeleteOrders(Request $request)
    {
        try {
        DB::table('inventjournaltransrepos')->truncate();
        return redirect()->route('order.index')
                ->with('message', 'Delete order successfully')
                ->with('isSuccess', true);
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())
            ->withInput()
            ->with('message', $e->errors())
            ->with('isSuccess', false);
        }
    }


    /*WAREHOUSE */
    public function warehouseorders($journalid)
    {
            $storename = Auth::user()->storeid;
            $utcDateTime = Carbon::now('UTC');
            $currentDate = $utcDateTime->setTimezone('Asia/Manila')->toDateString();

            $record = DB::table('inventjournaltables')
            ->where('journalid', $journalid)
            ->where('storeid', $storename)
            ->where('posted', 0)
            ->count();

            if ($record <= 0) {
                return redirect()->route('order.index')
                ->with('message', 'This has already been posted. If you want to view your order, please click on the Report module in the left sidebar')
                ->with('isError', true);

            } else {

            $inventjournaltransrepos = DB::table('inventjournaltransrepos as a')
            ->select('a.*', 'b.*', 'c.*')
            ->leftJoin('inventtables as b', 'a.itemid', '=', 'b.itemid')
            ->leftJoin('rboinventtables as c', 'b.itemid', '=', 'c.itemid')
            ->where('c.itemdepartment','NON PRODUCT')
            ->where('a.journalid',$journalid)
            ->where('a.storename', $storename)
            ->get();

            return Inertia::render('ItemOrders/warehouse', [
                'journalid' => $journalid,
                'inventjournaltransrepos' => $inventjournaltransrepos,
            ]);
            }

    }


    public function wViewOrders($journalid)
    {
            $storename = Auth::user()->storeid;
            $utcDateTime = Carbon::now('UTC');
            $currentDate = $utcDateTime->setTimezone('Asia/Manila')->toDateString();

            $record = DB::table('inventjournaltables')
            ->where('journalid', $journalid)
            ->where('storeid', $storename)
            ->where('posted', 0)
            ->count();

            if ($record <= 0) {
                return redirect()->route('order.index')
                ->with('message', 'This has already been posted. If you want to view your order, please click on the Report module in the left sidebar')
                ->with('isError', true);

            } else {

            $inventjournaltransrepos = DB::table('inventjournaltransrepos as a')
            ->select('a.*', 'b.*', 'c.*')
            ->leftJoin('inventtables as b', 'a.itemid', '=', 'b.itemid')
            ->leftJoin('rboinventtables as c', 'b.itemid', '=', 'c.itemid')
            ->where('c.itemdepartment','NON PRODUCT')
            ->where('a.journalid',$journalid)
            ->where('a.storename', $storename)
            ->where('a.counted', '!=', '0')
            ->get();

            return Inertia::render('ItemOrders/warehousecart', [
                'journalid' => $journalid,
                'inventjournaltransrepos' => $inventjournaltransrepos,
            ]);
            }
    }


    public function getOrdersForJournal($journalId)
{
    try {
        $storename = Auth::user()->storeid;
        
        $orders = DB::table('inventjournaltransrepos as c')
            ->select(
                'b.journalid',
                'b.POSTEDDATETIME as POSTEDDATETIME',
                'e.STOREID as STOREID', 
                'b.STOREID as STORENAME',
                'c.ITEMID as ITEMID',
                'z.WAREHOUSEDEPARTMENT as WAREHOUSEDEPARTMENT',
                'd.itemname as ITEMNAME',
                'c.COUNTED as COUNTED'
            )
            ->join('inventjournaltables as b', 'b.JOURNALID', '=', 'c.JOURNALID')
            ->leftJoin('inventtables as d', 'c.ITEMID', '=', 'd.itemid')
            ->leftJoin('rboinventtables as z', function($join) {
                $join->on('z.ITEMID', '=', 'd.itemid')
                    ->orWhere('z.ITEMID', '=', 'c.ITEMID');
            })
            ->leftJoin('rbostoretables as e', 'b.STOREID', '=', 'e.NAME')
            ->where('b.JOURNALID', '=', $journalId)
            ->where('c.storename', '=', $storename)
            ->get();
        
        \Log::info("Orders for journal {$journalId}:", [
            'count' => $orders->count(),
            'sample' => $orders->take(2)
        ]);
        
        return response()->json($orders);
    } catch (\Exception $e) {
        \Log::error("Error getting orders for journal {$journalId}: " . $e->getMessage(), [
            'trace' => $e->getTraceAsString()
        ]);
        
        return response()->json([
            'success' => false,
            'message' => 'Error retrieving orders: ' . $e->getMessage()
        ], 500);
    }
}

public function saveFile(Request $request)
{
    try {
        $content = $request->input('content');
        $filename = $request->input('filename');
        $folderName = $request->input('folderName');
        $journalId = $request->input('journalId');
        $storename = Auth::user()->storeid;

        if (!$content || !$filename || !$folderName) {
            return response()->json([
                'success' => false,
                'message' => 'Missing required parameters'
            ], 400);
        }

        \Log::info("Saving file for journal {$journalId}", [
            'filename' => $filename,
            'folderName' => $folderName,
            'contentLength' => strlen($content)
        ]);

        $folderPath = 'public/' . $folderName;
        
        $parts = explode('/', $folderName);
        $currentPath = 'public';
        
        foreach ($parts as $part) {
            if (empty($part)) continue;
            
            $currentPath .= '/' . $part;
            
            // Create directory if it doesn't exist
            if (!Storage::exists($currentPath)) {
                \Log::info("Creating directory: {$currentPath}");
                $result = Storage::makeDirectory($currentPath, 0755, true);
                if (!$result) {
                    \Log::error("Failed to create directory: {$currentPath}");
                    throw new \Exception("Failed to create directory: {$currentPath}");
                }
            }
        }

        $filePath = $folderPath . '/' . $filename;
        \Log::info("Writing file to: {$filePath}");
        
        $success = Storage::put($filePath, $content);
        
        if (!$success) {
            throw new \Exception("Failed to write file to {$filePath}");
        }

        if ($journalId) {
            $updated = DB::table('inventjournaltables')
                ->where('journalid', $journalId)
                ->where('STOREID', $storename)
                ->update([
                    'sent' => 1,
                ]);
                
            \Log::info("Updated sent status for journal {$journalId}: {$updated} rows affected");
        }

        return response()->json([
            'success' => true, 
            'path' => Storage::url($filePath),
            'message' => 'File saved successfully'
        ]);
    } catch (\Exception $e) {
        \Log::error("Error saving file: " . $e->getMessage(), [
            'trace' => $e->getTraceAsString(),
            'request' => $request->all()
        ]);
        
        return response()->json([
            'success' => false,
            'message' => 'Error saving file: ' . $e->getMessage()
        ], 500);
    }
}

public function postOrder(Request $request)
{
    try {
        \Log::info('Post order request received', [
            'request_data' => $request->all(),
            'content_type' => $request->header('Content-Type'),
            'csrf_header' => $request->header('X-CSRF-TOKEN')
        ]);
        
        // Validate request
        $validator = \Validator::make($request->all(), [
            'journalid' => 'required|string',
        ]);
        
        if ($validator->fails()) {
            \Log::warning('Validation failed', ['errors' => $validator->errors()->all()]);
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ], 422);
        }

        $journalId = (string) $request->journalid;
        $storeName = Auth::user()->storeid;
        
        \Log::info("Processing order posting for journal {$journalId}, store {$storeName}");
        
        // FIRST: Check if current time allows posting
        $timeCheckRequest = new Request(['journalid' => $journalId]);
        $timeCheckResponse = $this->checkOrderTime($timeCheckRequest);
        $timeCheckData = json_decode($timeCheckResponse->getContent(), true);
        
        if (!$timeCheckData['success']) {
            \Log::warning("Time check failed for journal {$journalId}: " . $timeCheckData['message']);
            return response()->json([
                'success' => false,
                'message' => $timeCheckData['message'],
                'cutoff_exceeded' => $timeCheckData['cutoff_exceeded'] ?? false
            ], 400);
        }
        
        // Current date for database operations
        $now = Carbon::now('Asia/Manila');
        $currentDateTime = $now->toDateString();
        
        // CRITICAL FIX: Use a database-level lock to prevent concurrent processing
        DB::beginTransaction();
        
        try {
            // FIXED: Explicitly select the columns we need, including 'posted'
            $journal = DB::table('inventjournaltables')
                ->select(['journalid', 'storeid', 'posted', 'sent', 'description']) // Explicitly select needed columns
                ->where('journalid', $journalId)
                ->where('storeid', $storeName)
                ->lockForUpdate() // This prevents other processes from modifying this record
                ->first();

            \Log::info("Journal query result", [
                'journal_found' => $journal ? 'Yes' : 'No',
                'journal_data' => $journal ? (array)$journal : null
            ]);

            if (!$journal) {
                \Log::warning("Journal not found: {$journalId}");
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Order not found.'
                ], 404);
            }
            
            // FIXED: Check if posted property exists and has a value
            $isPosted = isset($journal->posted) ? (int)$journal->posted : 0;
            
            \Log::info("Journal posting status", [
                'posted_property_exists' => isset($journal->posted),
                'posted_value' => $journal->posted ?? 'NULL',
                'is_posted' => $isPosted
            ]);
            
            // If already posted, return success but inform the client
            if ($isPosted == 1) {
                \Log::info("Journal {$journalId} is already posted, returning early");
                DB::rollBack();
                return response()->json([
                    'success' => true,
                    'message' => 'Order has already been posted.',
                    'already_posted' => true
                ]);
            }
            
            // FIXED: Get the items with proper column selection and aliases
            $items = DB::table('inventjournaltransrepos')
                ->select([
                    'ITEMID as itemid',
                    'COUNTED as counted', 
                    'ITEMDEPARTMENT as itemdepartment',
                    'STORENAME as storename'
                ])
                ->where('journalid', $journalId)
                ->where('storename', $storeName)
                ->where('counted', '>', 0)
                ->get();
                
            \Log::info("Found {$items->count()} items with non-zero counts for journal {$journalId}");
            
            // Debug: Log the first item to see its structure
            if ($items->count() > 0) {
                \Log::info("First item structure", [
                    'item_data' => (array)$items->first()
                ]);
            }
            
            if ($items->isEmpty()) {
                \Log::warning("No items with positive counts found for journal {$journalId}");
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'No items with positive counts found in this order.'
                ], 400);
            }

            // CRITICAL FIX: Check if any items already exist in inventjournaltrans for this journal
            $existingItems = DB::table('inventjournaltrans')
                ->where('JOURNALID', $journalId)
                ->count();
                
            if ($existingItems > 0) {
                \Log::warning("Items already exist in inventjournaltrans for journal {$journalId}, count: {$existingItems}");
                DB::rollBack();
                return response()->json([
                    'success' => true,
                    'message' => 'Order has already been processed.',
                    'already_posted' => true
                ]);
            }

            // Update the journal table first to mark as posted (this prevents other concurrent requests)
            $journalUpdateCount = DB::table('inventjournaltables')
                ->where('journalid', $journalId)
                ->where('storeid', $storeName)
                ->where(function($query) {
                    $query->where('posted', 0)
                          ->orWhereNull('posted'); // Handle NULL values
                })
                ->update([
                    'posted' => 1,
                    'sent' => 1,
                ]);
            
            if ($journalUpdateCount === 0) {
                \Log::warning("Journal {$journalId} was already posted by another process");
                DB::rollBack();
                return response()->json([
                    'success' => true,
                    'message' => 'Order has already been posted by another process.',
                    'already_posted' => true
                ]);
            }
            
            \Log::info("Updated journal table for journal {$journalId}, affected rows: {$journalUpdateCount}");

            // Update the status to 1 in the repos table
            $reposUpdateCount = DB::table('inventjournaltransrepos')
                ->where('journalid', $journalId)
                ->where('storename', $storeName)
                ->update([
                    'status' => 1
                ]);
                
            \Log::info("Updated repos status for journal {$journalId}, affected rows: {$reposUpdateCount}");

            // FIXED: Insert data to inventjournaltrans table with proper property access
            $insertedRows = 0;
            foreach ($items as $item) {
                try {
                    $inserted = DB::table('inventjournaltrans')->insertOrIgnore([
                        'JOURNALID' => $journalId,
                        'TRANSDATE' => $currentDateTime,
                        'ITEMID' => $item->itemid, // Now using lowercase alias
                        'ITEMDEPARTMENT' => $item->itemdepartment ?? 'REGULAR PRODUCT',
                        'ADJUSTMENT' => $item->counted,
                        'COSTPRICE' => 0.00,
                        'PRICEUNIT' => 0.00,
                        'SALESAMOUNT' => 0.00,
                        'INVENTONHAND' => 0,
                        'COUNTED' => $item->counted,
                        'REASONREFRECID' => '00001',
                        'VARIANTID' => 0,
                        'POSTED' => 1,
                        'UNITID' => 'PCS',
                        'updated_at' => now(),
                        'created_at' => now()
                    ]);
                    
                    if ($inserted) {
                        $insertedRows++;
                    }
                } catch (\Exception $insertError) {
                    \Log::error("Error inserting item {$item->itemid}: " . $insertError->getMessage());
                    throw $insertError;
                }
            }
            
            \Log::info("Inserted {$insertedRows} items for journal {$journalId}");

            // Double-check that we didn't create duplicates
            $finalItemCount = DB::table('inventjournaltrans')
                ->where('JOURNALID', $journalId)
                ->count();
                
            if ($finalItemCount > $items->count()) {
                \Log::error("Duplicate items detected! Expected: {$items->count()}, Found: {$finalItemCount}");
                // If duplicates detected, rollback and return error
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Duplicate items detected during posting. Please try again.'
                ], 500);
            }

            DB::commit();
            \Log::info("Successfully posted journal {$journalId} with {$insertedRows} items");
            
            return response()->json([
                'success' => true,
                'message' => 'Order has been successfully posted and sent',
                'insertedRows' => $insertedRows,
                'journalUpdateCount' => $journalUpdateCount
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e; // Re-throw to be caught by outer catch block
        }
        
    } catch (\Exception $e) {
        \Log::error("Error posting order: " . $e->getMessage(), [
            'exception' => get_class($e),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTraceAsString(),
            'request' => $request->all()
        ]);
        
        return response()->json([
            'success' => false,
            'message' => 'Error posting order: ' . $e->getMessage()
        ], 500);
    }
}

    public function checkOrderTime(Request $request)
{
    try {
        $request->validate([
            'journalid' => 'required|string',
        ]);
        
        $journalId = $request->journalid;
        $storeName = Auth::user()->storeid;
        
        // Get the store's cutoff time setting
        $store = \App\Models\rbostoretables::where('NAME', $storeName)->first();
        
        if (!$store) {
            return response()->json([
                'success' => false,
                'message' => 'Store information not found'
            ], 404);
        }
        
        // Get current time in your timezone
        $now = Carbon::now('Asia/Manila');
        $currentHour = (int)$now->format('H');
        $currentMinute = (int)$now->format('i');
        
        // Parse the store's cutoff time - default to 16:00 (4:00 PM)
        $cutoffTime = '16:00'; // Default cutoff time
        if ($store->CUTOFFTIME) {
            if (strlen($store->CUTOFFTIME) > 8) {
                $cutoffTime = Carbon::parse($store->CUTOFFTIME)->format('H:i');
            } else {
                $cutoffTime = $store->CUTOFFTIME;
            }
        }
        
        list($cutoffHour, $cutoffMinute) = explode(':', $cutoffTime);
        $cutoffHour = (int)$cutoffHour;
        $cutoffMinute = (int)$cutoffMinute;
        
        // Calculate total minutes for easier comparison
        $currentTotalMinutes = $currentHour * 60 + $currentMinute;
        $cutoffTotalMinutes = $cutoffHour * 60 + $cutoffMinute;
        
        // Check if current time is after cutoff time (4:00 PM by default)
        if ($currentTotalMinutes >= $cutoffTotalMinutes) {
            return response()->json([
                'success' => false,
                'message' => "Orders cannot be posted after the cutoff time ({$cutoffTime}). Please try again tomorrow after 12:00 AM.",
                'cutoff_exceeded' => true,
                'current_time' => $now->format('H:i'),
                'cutoff_time' => $cutoffTime
            ], 400);
        }
        
        // If we're here, posting is allowed
        return response()->json([
            'success' => true,
            'message' => 'Order can be posted',
            'current_time' => $now->format('H:i'),
            'cutoff_time' => $cutoffTime
        ]);
        
    } catch (\Exception $e) {
        \Log::error('Error checking order time: ' . $e->getMessage(), [
            'trace' => $e->getTraceAsString()
        ]);
        
        return response()->json([
            'success' => false,
            'message' => 'Error checking order time: ' . $e->getMessage()
        ], 500);
    }
}

public function checkOrderHasItems(Request $request, $journalId)
{
    try {
        // Get the total number of items in the journal with non-zero counts
        $itemsQuery = DB::table('inventjournaltransrepos')
            ->where('JOURNALID', $journalId)
            ->where('COUNTED', '>', 0);

        $itemCount = $itemsQuery->count();

        // If no items with non-zero counts exist
        if ($itemCount === 0) {
            return response()->json([
                'hasItems' => false,
                'totalQty' => 0,
                'totalOrders' => 0
            ]);
        }

        // Get the total quantity of items
        $totalQty = $itemsQuery->sum('COUNTED');

        // Get the total number of unique orders for this journal
        $totalOrders = DB::table('inventjournaltables')
            ->where('JOURNALID', $journalId)
            ->count();

        return response()->json([
            'hasItems' => true,
            'totalQty' => $totalQty,
            'totalOrders' => $totalOrders
        ]);
    } catch (\Exception $e) {
        \Log::error('Error checking order items: ' . $e->getMessage());
        return response()->json([
            'hasItems' => false,
            'error' => $e->getMessage()
        ], 500);
    }
}

public function recoverTxtFiles(Request $request, $date)
{
    try {
        \Log::info('TXT file recovery initiated', [
            'date' => $date,
            'user' => Auth::user()->name,
            'store' => Auth::user()->storeid,
            'request_data' => $request->all()
        ]);
        
        // Check user role
        $userRole = Auth::user()->role;
        $allowedRoles = ['ADMIN', 'SUPERADMIN'];
        
        \Log::info('User role: ' . $userRole);
        \Log::info('Allowed roles: ' . implode(', ', $allowedRoles));
        
        if (!in_array($userRole, $allowedRoles)) {
            \Log::warning('Unauthorized access attempt', [
                'user' => Auth::user()->name,
                'role' => $userRole
            ]);
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to perform this action'
            ], 403);
        }
        
        \Log::info('Role match found: ' . $userRole);
        
        // Validate input
        $request->validate([
            'storeData' => 'required|array',
        ]);
        
        $storeData = $request->storeData;
        $recoveredFiles = 0;
        $deletedFiles = 0;
        
        // Directory structure for TXT files
        $baseDir = $date;
        $basePath = storage_path('app/public/' . $baseDir);
        
        // Create base directory if it doesn't exist
        if (!file_exists($basePath)) {
            if (!mkdir($basePath, 0755, true)) {
                throw new \Exception("Failed to create directory path: {$basePath}");
            }
            \Log::info("Created directory: {$basePath}");
        }
        
        // Format date for file header (MM/DD/YYYY)
        $formattedDate = date('n/j/Y', strtotime(
            substr($date, 0, 4) . '-' . 
            substr($date, 4, 2) . '-' . 
            substr($date, 6, 2)
        ));
        
        // Process each store's data
        foreach ($storeData as $storeId => $storeInfo) {
            // Skip undefined store IDs
            if ($storeId === 'undefined' || empty($storeId)) {
                \Log::warning("Skipping undefined store ID");
                continue;
            }
            
            $items = $storeInfo['items'] ?? [];
            
            if (empty($items)) {
                \Log::warning("No items to recover for store {$storeId}");
                continue;
            }
            
            // Use consistent filename format: BW0004YYYYMMDD.txt
            $filename = "{$storeId}{$date}.txt";
            $filePath = $basePath . '/' . $filename;
            
            // First attempt to delete any existing file
            if (file_exists($filePath)) {
                \Log::info("Attempting to delete file: {$filename}");
                if (@unlink($filePath)) {
                    $deletedFiles++;
                    \Log::info("Successfully deleted file: {$filename}");
                } else {
                    \Log::warning("Failed to delete file: {$filename}");
                }
            }
            
            // Build new file content
            // First line is the store ID and date
            $fileContent = "{$storeId}|{$formattedDate}\n";
            
            foreach ($items as $itemId => $count) {
                // Ensure count is an integer and non-zero
                $count = (int)$count;
                if ($count > 0) {
                    // Format: "BO-ASS-PRO-020|50"
                    $fileContent .= "{$itemId}|{$count}\n";
                    \Log::debug("Adding item to {$filename}: {$itemId}|{$count}");
                }
            }
            
            if (count($items) === 0) {
                \Log::warning("No items with positive counts for store {$storeId}, skipping file creation");
                continue;
            }
            
            // Create the new TXT file
            \Log::info("Creating new TXT file for store {$storeId}", [
                'filename' => $filename,
                'item_count' => count(array_filter($items, function($count) { return (int)$count > 0; })),
                'full_path' => $filePath
            ]);
            
            // Write the file
            $bytesWritten = file_put_contents($filePath, $fileContent);
            
            if ($bytesWritten === false) {
                throw new \Exception("Failed to write to file: {$filePath}");
            }
            
            \Log::info("Successfully wrote {$bytesWritten} bytes to file");
            $recoveredFiles++;
        }
        
        \Log::info("TXT file recovery completed successfully", [
            'date' => $date,
            'files_deleted' => $deletedFiles,
            'files_recovered' => $recoveredFiles
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'TXT files have been successfully recreated',
            'filesDeleted' => $deletedFiles,
            'recoveredFiles' => $recoveredFiles
        ]);
    } catch (\Exception $e) {
        \Log::error('Error recovering TXT files: ' . $e->getMessage(), [
            'trace' => $e->getTraceAsString()
        ]);
        
        return response()->json([
            'success' => false,
            'message' => 'Error recovering TXT files: ' . $e->getMessage()
        ], 500);
    }
}

public function updateSystemFromTxtFiles($date)
{
    try {
        \Log::info('System update from TXT files initiated', [
            'date' => $date,
            'user' => Auth::user()->name,
            'store' => Auth::user()->storeid,
        ]);
        
        // Check user role
        $userRole = Auth::user()->role;
        $allowedRoles = ['ADMIN', 'SUPERADMIN'];
        
        \Log::info('User role: ' . $userRole);
        \Log::info('Allowed roles: ' . implode(', ', $allowedRoles));
        
        if (!in_array($userRole, $allowedRoles)) {
            \Log::warning('Unauthorized access attempt', [
                'user' => Auth::user()->name,
                'role' => $userRole
            ]);
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to perform this action'
            ], 403);
        }
        
        // Directory structure for TXT files
        $baseDir = $date;
        $basePath = storage_path('app/public/' . $baseDir);
        
        if (!file_exists($basePath)) {
            \Log::warning('Directory does not exist: ' . $basePath);
            return response()->json([
                'success' => false,
                'message' => 'No TXT files found for this date',
            ]);
        }
        
        \Log::info('Reading directory contents: ' . $basePath);
        
        // Get all TXT files in the directory
        $files = [];
        if ($handle = opendir($basePath)) {
            while (false !== ($entry = readdir($handle))) {
                if ($entry != "." && $entry != ".." && stripos($entry, '.txt') !== false) {
                    $files[] = $basePath . '/' . $entry;
                    \Log::info('Found file: ' . $entry);
                }
            }
            closedir($handle);
        }
        
        if (empty($files)) {
            \Log::warning('No TXT files found in directory');
            return response()->json([
                'success' => false,
                'message' => 'No TXT files found for this date',
            ]);
        }
        
        \Log::info('Found ' . count($files) . ' TXT files in directory');
        
        $recordsUpdated = 0;
        $filesParsed = 0;
        $storesProcessed = [];
        
        // Format date for database query (YYYY-MM-DD)
        $formattedDate = substr($date, 0, 4) . '-' . 
                          substr($date, 4, 2) . '-' . 
                          substr($date, 6, 2);
        
        // Process each TXT file
        foreach ($files as $file) {
            $filename = basename($file);
            $filesParsed++;
            
            \Log::info('Processing file: ' . $filename);
            
            // Extract store ID from filename (e.g., BW0004YYYYMMDD.txt)
            $storeId = '';
            if (preg_match('/^([A-Za-z0-9]+)(\d{8})\.txt$/', $filename, $matches)) {
                $storeId = $matches[1];
                \Log::info('File header - StoreID: ' . $storeId . ', Date: ' . $formattedDate);
            } else {
                \Log::warning('Could not extract store ID from filename: ' . $filename);
                continue;
            }
            
            $storesProcessed[] = $storeId;
            
            // Read file contents
            $content = file_get_contents($file);
            if ($content === false) {
                \Log::warning('Failed to read file: ' . $filename);
                continue;
            }
            
            // Parse file contents line by line
            $lines = explode("\n", trim($content));
            $itemsUpdated = 0;
            
            // Skip the first line as it contains the header
            for ($i = 1; $i < count($lines); $i++) {
                $line = trim($lines[$i]);
                if (empty($line)) continue;
                
                // Parse using the pipe-delimited format: "ItemID|Count"
                $parts = explode('|', $line);
                if (count($parts) >= 2) {
                    $itemId = trim($parts[0]);
                    $count = (int)trim($parts[1]);
                    
                    if (!empty($itemId) && $count > 0) {
                        try {
                            // First, try to directly update in the main tables that show up in the discrepancy report
                            
                            // 1. First attempt: Update in inventjournaltrans table based on the most recent entries
                            \Log::info("Attempting to update {$itemId} count to {$count} in recent inventory journals");
                            
                            // Find recent journal IDs for this store 
                            $journals = DB::table('inventjournaltables')
                                ->where('STOREID', $storeId)
                                ->orderBy('POSTEDDATETIME', 'desc')
                                ->limit(5) // Get the most recent 5 journals
                                ->pluck('JOURNALID')
                                ->toArray();
                                
                            if (!empty($journals)) {
                                \Log::info("Found recent journals for store {$storeId}: " . implode(', ', $journals));
                                
                                $updated = DB::table('inventjournaltrans')
                                    ->where('ITEMID', $itemId)
                                    ->whereIn('JOURNALID', $journals)
                                    ->update(['COUNTED' => $count]);
                                    
                                if ($updated) {
                                    $itemsUpdated += $updated;
                                    \Log::info("Updated item {$itemId} count to {$count} in inventjournaltrans table");
                                    continue; // Move to next item if successful
                                }
                            }
                            
                            // 2. Second attempt: Try to update in the repos table
                            \Log::info("Attempting to update {$itemId} count to {$count} in inventjournaltransrepos");
                            
                            $reposUpdated = DB::table('inventjournaltransrepos')
                                ->where('ITEMID', $itemId)
                                ->where('STORENAME', $storeId)
                                ->update(['COUNTED' => $count]);
                                
                            if ($reposUpdated) {
                                $itemsUpdated += $reposUpdated;
                                \Log::info("Updated item {$itemId} count to {$count} in inventjournaltransrepos table");
                                continue; // Move to next item if successful
                            }
                            
                            // 3. Last attempt: Try to find any record that might match in inventjournaltrans
                            \Log::info("Attempting to update {$itemId} count to {$count} in any inventory journal");
                            
                            // Look for this item in ANY journal for this store
                            $anyJournals = DB::table('inventjournaltables as t')
                                ->join('inventjournaltrans as tr', 't.JOURNALID', '=', 'tr.JOURNALID')
                                ->where('t.STOREID', $storeId)
                                ->where('tr.ITEMID', $itemId)
                                ->select('tr.JOURNALID')
                                ->pluck('tr.JOURNALID')
                                ->toArray();
                                
                            if (!empty($anyJournals)) {
                                \Log::info("Found some journals containing item {$itemId} for store {$storeId}: " . implode(', ', $anyJournals));
                                
                                $updated = DB::table('inventjournaltrans')
                                    ->where('ITEMID', $itemId)
                                    ->whereIn('JOURNALID', $anyJournals)
                                    ->update(['COUNTED' => $count]);
                                    
                                if ($updated) {
                                    $itemsUpdated += $updated;
                                    \Log::info("Updated item {$itemId} count to {$count} in inventjournaltrans table");
                                    continue; // Move to next item if successful
                                }
                            }
                            
                            // If we get here, no updates were successful
                            \Log::warning("Could not find any matching record to update for item {$itemId} in store {$storeId}");
                            
                        } catch (\Exception $e) {
                            \Log::error("Error updating item {$itemId}: " . $e->getMessage(), [
                                'exception' => get_class($e),
                                'file' => $e->getFile(),
                                'line' => $e->getLine(),
                                'trace' => $e->getTraceAsString()
                            ]);
                        }
                    }
                } else {
                    \Log::warning('Invalid line format in ' . $filename . ': ' . $line);
                }
            }
            
            $recordsUpdated += $itemsUpdated;
            \Log::info("Completed processing file {$filename}: {$itemsUpdated} items updated");
        }
        
        // Remove any duplicates from the storesProcessed array
        $storesProcessed = array_unique($storesProcessed);
        
        \Log::info("System update from TXT files completed successfully", [
            'date' => $date,
            'files_parsed' => $filesParsed,
            'records_updated' => $recordsUpdated,
            'stores_processed' => implode(', ', $storesProcessed)
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'System data has been successfully updated from TXT files',
            'filesParsed' => $filesParsed,
            'recordsUpdated' => $recordsUpdated,
            'storesProcessed' => implode(', ', $storesProcessed)
        ]);
    } catch (\Exception $e) {
        \Log::error('Error updating system from TXT files: ' . $e->getMessage(), [
            'trace' => $e->getTraceAsString()
        ]);
        
        return response()->json([
            'success' => false,
            'message' => 'Error updating system from TXT files: ' . $e->getMessage()
        ], 500);
    }
}

public function getTxtFiles($date)
{
    try {
        \Log::info('getTxtFiles called with date: ' . $date);
        
        // Clear any PHP caches to ensure fresh file reading
        if (function_exists('opcache_reset')) {
            opcache_reset();
        }
        clearstatcache(true);
        
        $basePath = storage_path('app/public/' . $date);
        
        if (!file_exists($basePath)) {
            \Log::warning('Directory does not exist: ' . $basePath);
            return response()->json([
                'success' => false,
                'message' => 'No TXT files found for this date',
                'txtFileData' => []
            ]);
        }
        
        \Log::info('Reading directory contents: ' . $basePath);
        
        // Get all TXT files in the directory
        $files = [];
        if ($handle = opendir($basePath)) {
            while (false !== ($entry = readdir($handle))) {
                if ($entry != "." && $entry != ".." && stripos($entry, '.txt') !== false) {
                    $files[] = $basePath . '/' . $entry;
                    \Log::info('Found file: ' . $entry);
                }
            }
            closedir($handle);
        }
        
        if (empty($files)) {
            \Log::warning('No TXT files found in directory');
            return response()->json([
                'success' => false,
                'message' => 'No TXT files found for this date',
                'txtFileData' => []
            ]);
        }
        
        \Log::info('Found ' . count($files) . ' TXT files in directory');
        
        $txtFileData = [];
        
        foreach ($files as $file) {
            $filename = basename($file);
            
            \Log::info('Processing file: ' . $filename);
            
            // Extract store ID from filename (e.g., BW0004YYYYMMDD.txt)
            $storeId = '';
            if (preg_match('/^([A-Za-z0-9]+)(\d{8})\.txt$/', $filename, $matches)) {
                $storeId = $matches[1];
                \Log::info('File header - StoreID: ' . $storeId . ', Date: ' . date('n/j/Y', strtotime(substr($matches[2], 0, 4) . '-' . substr($matches[2], 4, 2) . '-' . substr($matches[2], 6, 2))));
            } else {
                \Log::warning('Could not extract store ID from filename: ' . $filename);
                continue;
            }
            
            // Read file contents
            $content = file_get_contents($file);
            if ($content === false) {
                \Log::warning('Failed to read file: ' . $filename);
                continue;
            }
            
            // Initialize the store data structure
            $txtFileData[$storeId] = [
                'date' => date('n/j/Y'),  // Use current date format: MM/DD/YYYY
                'items' => []
            ];
            
            // Parse file contents line by line
            $lines = explode("\n", trim($content));
            
            foreach ($lines as $line) {
                $line = trim($line);
                if (empty($line)) continue;
                
                // Parse using the "ItemID => Count" format
                if (preg_match('/^([A-Za-z0-9\-]+)\s*=>\s*(\d+)$/', $line, $matches)) {
                    $itemId = trim($matches[1]);
                    $count = (int)trim($matches[2]);
                    
                    $txtFileData[$storeId]['items'][$itemId] = $count;
                    \Log::debug('Parsed item: ' . $itemId . ' => ' . $count);
                } else {
                    // Try comma-separated format as fallback
                    $parts = explode(',', $line);
                    if (count($parts) >= 2) {
                        $itemId = trim($parts[0]);
                        $count = (int)trim($parts[1]);
                        
                        $txtFileData[$storeId]['items'][$itemId] = $count;
                        \Log::debug('Parsed item (comma format): ' . $itemId . ' => ' . $count);
                    } else {
                        \Log::warning('Invalid line format in ' . $filename . ': ' . $line);
                    }
                }
            }
        }
        
        \Log::info('Returning data for ' . count($txtFileData) . ' stores');
        
        return response()->json([
            'success' => true,
            'txtFileData' => $txtFileData
        ]);
    } catch (\Exception $e) {
        \Log::error('Error getting TXT files: ' . $e->getMessage(), [
            'trace' => $e->getTraceAsString()
        ]);
        
        return response()->json([
            'success' => false,
            'message' => 'Error getting TXT files: ' . $e->getMessage(),
            'txtFileData' => []
        ], 500);
    }
}

private function getCleanStoreId($filename)
{
    // Extract store ID from filename pattern like BW0001YYYYMMDD.txt
    if (preg_match('/BW(\d+)\d{8}\.txt$/', $filename, $matches)) {
        return $matches[1];
    }
    
    // Try another pattern in case the "BW" prefix is missing
    if (preg_match('/^(\d+)\d{8}\.txt$/', $filename, $matches)) {
        return $matches[1];
    }
    
    // For BWBW0001YYYYMMDD.txt pattern (with duplicate BW)
    if (preg_match('/BWBW(\d+)\d{8}\.txt$/', $filename, $matches)) {
        return $matches[1];
    }
    
    // Return just the filename without extension if all else fails
    return pathinfo($filename, PATHINFO_FILENAME);
}

public function getStoresWithOrders($date)
{
    try {
        // Get all stores that had orders on this date
        $stores = DB::table('inventjournaltables as a')
            ->join('rbostoretables as b', 'a.STOREID', '=', 'b.NAME')
            ->where(DB::raw('DATE(a.POSTEDDATETIME)'), '=', 
                date('Y-m-d', strtotime(substr($date, 0, 4) . '-' . substr($date, 4, 2) . '-' . substr($date, 6, 2))))
            ->select('b.NAME', 'b.STOREID')
            ->distinct()
            ->get();
            
        return response()->json([
            'success' => true,
            'stores' => $stores
        ]);
    } catch (\Exception $e) {
        \Log::error('Error getting stores with orders: ' . $e->getMessage(), [
            'trace' => $e->getTraceAsString()
        ]);
        
        return response()->json([
            'success' => false,
            'message' => 'Error getting stores with orders: ' . $e->getMessage(),
            'stores' => []
        ], 500);
    }
}


}
