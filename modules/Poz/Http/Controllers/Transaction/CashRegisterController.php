<?php

namespace Modules\Poz\Http\Controllers\Transaction;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Poz\Models\CashRegister;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class CashRegisterController extends Controller
{
    private function getActiveRegister($outletId)
    {
        return CashRegister::where('status', 'open')
            ->where('casier_id', Auth::id())
            ->whereHas('outlets', function($q) use ($outletId) {
                $q->where('outlets.id', $outletId);
            })->first();
    }

    public function update(Request $request)
    {
        $request->validate([
            'amount'   => 'required|numeric',
            'log_type' => 'nullable|in:transaction,adjustment',
            'reason'   => 'nullable|string|max:255'
        ]);

        $currentOutletId = Auth::user()->outlet_id;

        try {
            DB::beginTransaction();

            $register = $this->getActiveRegister($currentOutletId);

            if (!$register) {
                return response()->json(['success' => false, 'message' => 'Kasir belum dibuka!'], 403);
            }

            $amount = (float) $request->amount;
            $status = $amount >= 0 ? 'plus' : 'minus';
            $absoluteAmount = abs($amount);

            if ($status === 'minus') {
                if ($register->money < $absoluteAmount) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Saldo kasir tidak mencukupi!'
                    ], 400);
                }
                $register->decrement('money', $absoluteAmount);
            } else {
                $register->increment('money', $absoluteAmount);
            }

            $register->logCash()->create([
                'status'   => $status,
                'money'    => $absoluteAmount,
                'log_type' => $request->log_type ?? 'adjustment',
                'reason'   => $request->reason,
            ]);

            DB::commit();

            $register->refresh();

            return response()->json([
                'success' => true,
                'message' => 'Saldo berhasil diperbarui!',
                'new_balance' => 'Rp ' . number_format($register->money, 0, ',', '.')
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function open()
    {
        $currentOutletId = Auth::user()->outlet_id;

        if (!$currentOutletId) {
            return response()->json(['success' => false, 'message' => 'User tidak memiliki outlet!'], 400);
        }

        try {
            DB::beginTransaction();

            $existingOpen = $this->getActiveRegister($currentOutletId);

            if ($existingOpen) {
                return response()->json(['success' => false, 'message' => 'Anda masih memiliki sesi kasir yang terbuka!'], 400);
            }

            $register = CashRegister::create([
                'casier_id' => Auth::id(),
                'money'     => 0,
                'status'    => 'open',
                'opened_at' => Carbon::now(),
            ]);

            $register->outlets()->attach($currentOutletId);

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Sesi kasir baru berhasil dibuka!']);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Gagal: ' . $e->getMessage()], 500);
        }
    }

    public function close()
    {
        $currentOutletId = Auth::user()->outlet_id;

        try {
            DB::beginTransaction();

            $register = $this->getActiveRegister($currentOutletId);

            if ($register) {
                $register->update([
                    'status'    => 'closed',
                    'closed_at' => Carbon::now(),
                ]);
            }

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Sesi kasir berhasil ditutup!']);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
