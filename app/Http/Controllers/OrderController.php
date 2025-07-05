<?php

namespace App\Http\Controllers;
use App\Models\inventjournaltables; 
use App\Models\nubersequencevalues; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Carbon\Carbon;

class OrderController extends Controller
{
    public function index()
{
    $storeId = Auth::user()->storeid;
    $role = Auth::user()->role;

    if($role == "ADMIN"){
        $inventjournaltables = DB::table('inventjournaltables AS a')
        ->select('a.journalid', 'a.storeid', 'a.description',
                DB::raw('SUM(CAST(b.COUNTED AS UNSIGNED)) AS QTY'),
                DB::raw('SUM(CAST(c.priceincltax AS DECIMAL(10,2))) AS AMOUNT'),
                'a.posted', 'a.sent', 'a.updated_at', 'a.journaltype', 'a.posteddatetime as createddatetime', 
                DB::raw('DATE(a.created_at) as datecreated')) // Added datecreated column
        ->leftJoin('inventjournaltrans AS b', 'b.JOURNALID', '=', 'a.journalid')
        ->leftJoin('inventtablemodules AS c', 'c.itemid', '=', 'b.ITEMID')
        ->groupBy('a.journalid', 'a.storeid', 'a.description',
                'a.posted', 'a.sent', 'a.updated_at', 'a.journaltype', 'a.posteddatetime', 'a.created_at')
        ->get();
    } else {
        $inventjournaltables = DB::table('inventjournaltables AS a')
        ->select('a.journalid', 'a.storeid', 'a.description',
                DB::raw('SUM(CAST(b.COUNTED AS UNSIGNED)) AS qty'),
                DB::raw('SUM(CAST(c.priceincltax AS DECIMAL(10,2)) * CAST(b.COUNTED AS UNSIGNED)) AS amount'),
                'a.posted', 'a.sent', 'a.updated_at', 'a.journaltype', 'a.posteddatetime as createddatetime',
                DB::raw('DATE(a.created_at) as datecreated')) // Added datecreated column
        ->leftJoin('inventjournaltrans AS b', 'b.JOURNALID', '=', 'a.journalid')
        ->leftJoin('inventtablemodules AS c', 'c.itemid', '=', 'b.ITEMID')
        ->where('storeid', '=', $storeId)
        ->groupBy('a.journalid', 'a.storeid', 'a.description',
                'a.posted', 'a.sent', 'a.updated_at', 'a.journaltype', 'a.posteddatetime', 'a.created_at')
        ->get();
    }

    // Get the most recent posted date from the database
    $currentDateOnly = DB::table('inventjournaltables')
        ->orderBy('posteddatetime', 'DESC') 
        ->limit(1)
        ->value('POSTEDDATETIME');

    $currentDateTime = \Carbon\Carbon::parse($currentDateOnly)->toDateString();

    // Initialize orders array
    $orders = [];
    
    // For store users, get order data
    if ($role === "STORE") {
        // Remove time restriction - always get today's orders regardless of time
        $orders = DB::table('inventjournaltables as b')
            ->select(
                'b.journalid',
                'b.POSTEDDATETIME as POSTEDDATETIME',
                'e.STOREID as STOREID', 
                'b.STOREID as STORENAME',
                'd.itemid as ITEMID',
                'z.WAREHOUSEDEPARTMENT as WAREHOUSEDEPARTMENT',
                'd.itemname as ITEMNAME',
                'c.COUNTED as COUNTED'
            )
            ->leftJoin('inventjournaltransrepos as c', 'b.JOURNALID', '=', 'c.JOURNALID')
            ->leftJoin('inventtables as d', 'c.ITEMID', '=', 'd.itemid')
            ->leftJoin('rboinventtables as z', 'z.ITEMID', '=', 'd.itemid')
            ->leftJoin('rbostoretables as e', 'b.STOREID', '=', 'e.NAME')
            ->whereRaw("DATE(b.createddatetime) = ?", [$currentDateTime]) 
            ->where('b.POSTED', '=', '0')
            ->where('b.STOREID', '=', $storeId) 
            ->get();
    }

    return Inertia::render('Orders/Index', [
        'inventjournaltables' => $inventjournaltables,
        'orders' => $orders
    ]);
}

   
    public function create()
    {
        //
    }

    public function checkOrderDate($date, $storeId)
{
    $exists = DB::table('inventjournaltables')
        ->whereDate('CREATEDDATETIME', $date)
        ->where('STOREID', $storeId)
        ->exists();

    return response()->json(['exists' => $exists]);
}


public function store(Request $request)
{
    DB::beginTransaction();

    try {
        $request->validate([
            'orderDate' => 'required|date',
            'orderTime' => 'nullable|date_format:h:i A', // 12-hour format with AM/PM
        ]);

        // Parse the order date
        $orderDate = Carbon::parse($request->orderDate)->setTimezone('Asia/Manila');
        
        // Handle time component in 12-hour format
        if ($request->filled('orderTime')) {
            // Parse the time in 12-hour format
            $timeObj = Carbon::createFromFormat('h:i A', $request->orderTime, 'Asia/Manila');
            $orderDate->setHour($timeObj->hour)
                      ->setMinute($timeObj->minute)
                      ->setSecond(0);
        } else {
            // If no time provided, use current time in 12-hour format
            $currentTime = Carbon::now('Asia/Manila');
            $orderDate->setHour($currentTime->hour)
                      ->setMinute($currentTime->minute)
                      ->setSecond($currentTime->second);
        }
        
        $storeId = Auth::user()->storeid;
        $userId = Auth::user()->id;

        // Update the check for existing orders to use only the date part
        $existingOrder = DB::table('inventjournaltables')
            ->whereDate('CREATEDDATETIME', $orderDate->toDateString())
            ->where('STOREID', $storeId)
            ->exists();

        if ($existingOrder) {
            throw new \Exception('An order already exists for the selected date.');
        }

        $nextRec = DB::table('nubersequencevalues')
            ->where('storeid', $storeId)
            ->lockForUpdate()
            ->value('nextrec');

        $nextRec = $nextRec !== null ? (int)$nextRec + 1 : 1;

        DB::table('nubersequencevalues')
            ->where('STOREID', $storeId)
            ->update(['NEXTREC' => $nextRec]);

        $journalId = $userId . str_pad($nextRec, 8, '0', STR_PAD_LEFT);
        $description = "TR" . $journalId;

        $newJournal = Inventjournaltables::create([
            'JOURNALID' => $journalId,
            'STOREID' => $storeId,
            'DESCRIPTION' => $description,
            'POSTED' => "0",
            'SENT' => "0",
            'POSTEDDATETIME' => $orderDate, // Now includes proper time in 12-hour format
            'JOURNALTYPE' => "1",
            'DELETEPOSTEDLINES' => "0",
            'CREATEDDATETIME' => now(),
        ]);

        DB::commit();

        return redirect()->route('order.index', ['role' => Auth::user()->role])
            ->with('message', 'Order Created Successfully')
            ->with('isSuccess', true);

    } catch (\Exception $e) {
        DB::rollBack();
        return redirect()->route('order.index')
            ->with('message', $e->getMessage())
            ->with('isError', true);
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
        //
    }


    public function destroy(string $id)
    {
        //
    }

    public function generatetxtfile()
    {
        $utcDateTime = Carbon::now('UTC');
        $currentDateTime = $utcDateTime->setTimezone('Asia/Manila')->toDateString();
        $storeId = Auth::user()->storeid;
        $role = Auth::user()->role;

        if($role == "STORE"){
            $orders = DB::table('inventjournaltables as b')
            ->select('b.journalid', 'b.POSTEDDATETIME as POSTEDDATETIME', 'e.STOREID as STOREID', 'b.STOREID as STORENAME', 'd.itemid as ITEMID', 'd.itemname as ITEMNAME', 'c.COUNTED as COUNTED')
            ->leftJoin('inventjournaltrans as c', 'b.JOURNALID', '=', 'c.JOURNALID')
            ->leftJoin('inventtables as d', 'c.ITEMID', '=', 'd.itemid')
            ->leftJoin('rbostoretables as e', 'b.STOREID', '=', 'e.NAME')
            ->whereRaw("DATE(b.createddatetime) = ?", [$currentDateTime])
            ->where('b.POSTED', '=', '1')
            ->where('e.STOREID', '=', 'BW0002')
            ->get();
        }

        return Inertia::render('Reports/TxtFile', ['orders' => $orders]);
    }
}
