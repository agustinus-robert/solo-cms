<?php

namespace Modules\Poz\Http\Controllers\Schedule;

use Modules\Reference\Http\Controllers\Controller;
use Yajra\DataTables\DataTables as Table;
use Modules\Poz\Models\Product;
use Illuminate\Http\Request;
use Modules\Poz\Models\Supplier;
use Modules\Poz\Models\Adjustment;
use Illuminate\Support\Facades\Auth;
use Modules\Poz\Models\Purchase;
use Modules\Poz\Models\Sale;
use Modules\Poz\Models\SaleDirect;
use Modules\Poz\Models\SupplierSchedule;
use Modules\Account\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SupplierScheduleController extends Controller
{
    /**
     * Update: Sekarang mencari User ID berdasarkan Supplier ID
     */
    private function sendScheduleNotification($time, $outletId, $supplierIds = [])
    {
        try {
            if (!$outletId || empty($supplierIds)) return;

            $timeLabels = [
                'morning'   => 'Pagi',
                'afternoon' => 'Siang',
                'evening'   => 'Sore',
            ];

            $label = $timeLabels[$time] ?? ucfirst($time);

            $targetUserIds = Supplier::whereIn('id', $supplierIds)
                ->whereNotNull('user_id')
                ->pluck('user_id')
                ->toArray();

            if (empty($targetUserIds)) return;
            $targetUsers = User::whereIn('id', $targetUserIds)->get();

            foreach ($targetUsers as $user) {
                auth()->user()->sendSystemNotification([
                    'user_id_target' => $user->id,
                    'title'   => 'Jadwal Anda Diperbarui',
                    'message' => "Jadwal Anda untuk shift <strong>{$label}</strong> telah diperbarui oleh <strong>" . auth()->user()->name . "</strong>.",
                    'link'    => route('poz::schedule.supplier_schedule.index') . '?outlet=' . $outletId,
                    'icon'    => 'bx bx-calendar-event',
                    'color'   => 'info',
                ], $outletId);
            }
        } catch (\Exception $e) {
            Log::error("Schedule Notification Error: " . $e->getMessage());
        }
    }

    /**
     * Show the dashboard page.
     */
    public function index(Request $request)
    {
        $outletId = $request->outlet;

        $timeLabels = [
            'morning' => 'Pagi',
            'afternoon' => 'Siang',
            'evening' => 'Sore',
        ];

        $schedules = collect($timeLabels)->map(function ($label, $key) use ($outletId) {
            $total = SupplierSchedule::where('time', $key)
                ->whereNull('deleted_at')
                ->whereHas('supplier.outlets', function ($query) use ($outletId) {
                    $query->where('outlet_id', $outletId);
                })
                ->distinct('supplier_id')
                ->count('supplier_id');

            return (object)[
                'label' => $label,
                'key' => $key,
                'total_supplier' => $total,
                'url' => route('poz::schedule.supplier_schedule.show', [
                    'supplier_schedule' => $key,
                    'outlet' => $outletId,
                ])
            ];
        });

        return view('poz::schedule.supplier_schedule', [
            'title' => 'Supplier Schedule',
            'schedules' => $schedules,
            'outletId' => $outletId
        ]);
    }

    public function show($supplier_schedule){
        $suppliers = Supplier::whereNull('deleted_at')->get();
        $products = Product::whereNull('deleted_at')->get();
        $prodSupp = SupplierSchedule::whereNull('deleted_at')->where('time', $supplier_schedule)->get();

        return view('poz::schedule.show', compact('suppliers', 'products', 'supplier_schedule', 'prodSupp'));
    }

    public function store(Request $request)
    {
        $time = strtolower($request->time);
        $schedules = $request->input('schedules', []);
        $outletId = $request->query('outlet', auth()->user()->current_outlet_id);
        $targetSupplierIds = collect($schedules)->pluck('supplier_id')->unique()->filter()->toArray();

        DB::beginTransaction();

        try {
            foreach ($schedules as $schedule) {
                $supplierId = $schedule['supplier_id'] ?? null;
                $productId = $schedule['product_id'] ?? null;

                if (!$supplierId || !$productId) {
                    continue;
                }

                SupplierSchedule::where('supplier_id', $supplierId)
                    ->where('product_id', $productId)
                    ->where('time', $time)
                    ->delete();

                SupplierSchedule::create([
                    'supplier_id' => $supplierId,
                    'product_id' => $productId,
                    'day'        => null,
                    'time'       => $time,
                ]);
            }

            DB::afterCommit(function () use ($time, $outletId, $targetSupplierIds) {
                $this->sendScheduleNotification($time, $outletId, $targetSupplierIds);
            });

            DB::commit();

            return redirect(
                route('poz::schedule.supplier_schedule.index', ['outlet' => $outletId])
            )->with('success', 'Shift berhasil disimpan.');
        } catch (\Throwable $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Gagal menyimpan jadwal: ' . $e->getMessage());
        }
    }


   public function supplierScheduleTable(Request $request)
   {
        $outletId = $request->outlet;

        $timeLabels = [
            'morning' => 'Pagi',
            'afternoon' => 'Siang',
            'evening' => 'Sore',
        ];

        $data = collect($timeLabels)->map(function ($label, $key) use ($outletId) {
            $total = SupplierSchedule::where('time', $key)
                ->whereNull('deleted_at')
                ->whereHas('supplier.outlets', function ($query) use ($outletId) {
                    $query->where('outlet_id', $outletId);
                })
                ->distinct('supplier_id')
                ->count('supplier_id');

            return (object)[
                'time' => $label, // Untuk ditampilkan di kolom 'time'
                'time_key' => $key, // Untuk dikirim via URL
                'total_supplier' => $total,
            ];
        });

        return Table::of($data)
            ->addIndexColumn()
            ->addColumn('action', function ($row) use ($outletId) {
                return '<a href="' . route('poz::schedule.supplier_schedule.show', [
                    'supplier_schedule' => $row->time_key,
                    'outlet' => $outletId,
                ]) . '" class="btn btn-sm btn-primary">Manage</a>';
            })
            ->rawColumns(['action'])
            ->make(true);
    }
}
