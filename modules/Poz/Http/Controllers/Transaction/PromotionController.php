<?php

namespace Modules\Poz\Http\Controllers\Transaction;

use Modules\Reference\Http\Controllers\Controller;
use Yajra\DataTables\DataTables as Table;
use Modules\Poz\Models\ProductPromotion;
use Modules\Poz\Models\Product;
use App\Notifications\GlobalGenericNotification;
use Modules\Account\Models\User;
use Modules\Poz\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class PromotionController extends Controller
{
    private function sendPromotionNotification($promotion, $type = 'create')
    {
        try {
            $details = [
                'title'   => ($type == 'create') ? 'Promosi Baru' : 'Promosi Diperbarui',
                'message' => ($type == 'create')
                    ? "Promosi <strong>'{$promotion->name}'</strong> telah berhasil dibuat."
                    : "Data promosi <strong>'{$promotion->name}'</strong> baru saja diubah.",
                'link'    => route('poz::transaction.product-promotion.index'),
                'icon'    => ($type == 'create') ? 'bx bx-megaphone' : 'bx bx-edit-alt',
                'color'   => ($type == 'create') ? 'success' : 'info'
            ];

            \Modules\Account\Models\User::broadcastSystemNotification($details);
        } catch (\Exception $e) {
            \Log::error("Gagal broadcast promo: " . $e->getMessage());
        }
    }

    public function index()
    {
        $data = [
            'title' => 'Daftar Promosi',
            'column' => [
                dbuilder_table('image', 'Gambar', false, false, 'w10'),
                dbuilder_table('name', 'Nama', false, true),
                dbuilder_table('action', 'Aksi')
            ]
        ];

        return view('poz::transaction.promotion.index', $data);
    }

    public function create(Request $request)
    {
        return view('poz::transaction.promotion.index', [
            'action'     => 'create',
            'promotion'  => null,
            'outlet_id'  => $request->outlet,
            'products'   => Product::orderBy('name', 'asc')->get(),
            'categories' => Category::orderBy('name', 'asc')->get(),
        ]);
    }

    private function formatConfigWithModels($config, $type)
    {
        if ($type == 1) { // Tipe Per Produk
            if (isset($config['product_id'])) {
                $config['product_model'] = Product::class;
            }
            if (isset($config['bonus_product_id'])) {
                $config['bonus_product_model'] = Product::class;
            }
        }
        elseif ($type == 2) { // Tipe Bundle
            if (isset($config['bundle_products']) && is_array($config['bundle_products'])) {
                foreach ($config['bundle_products'] as $key => $val) {
                    $config['bundle_products'][$key]['model'] = Product::class;
                }
            }
            if (isset($config['bundle_categories']) && is_array($config['bundle_categories'])) {
                foreach ($config['bundle_categories'] as $key => $val) {
                    $config['bundle_categories'][$key]['model'] = Category::class;
                }
            }
        }
        return $config;
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:255',
            'type'        => 'required|integer',
            'start_date'  => 'required|date',
            'end_date'    => 'nullable|date|after_or_equal:start_date',
            'config'      => 'required|array'
        ]);

        DB::beginTransaction();
        try {
            $data = $request->only(['name', 'type', 'start_date', 'end_date']);
            $data['config'] = $this->formatConfigWithModels($request->config, $request->type);

            if ($request->hasFile('image')) {
                $file = $request->file('image');
                $filename = time() . '_' . $file->getClientOriginalName();
                $location = 'promotion';
                $file->move(public_path('uploads/' . $location), $filename);
                $data['location'] = $location;
                $data['image_name'] = $filename;
            }

            $data['created_by'] = Auth::id();

            $promotion = ProductPromotion::create($data);
            $promotion->outlets()->sync($request->outlet_id);

            $details = [
                'title'   => 'Promosi Baru',
                'message' => "Promosi '{$promotion->name}' telah berhasil dibuat.",
                'link'    => '#',
                'icon'    => 'bx bx-megaphone',
                'color'   => 'success'
            ];

            DB::afterCommit(function () use ($promotion) {
                $this->sendPromotionNotification($promotion, 'create');
            });

            DB::commit();

            return redirect()
                ->route('poz::transaction.product-promotion.index', ['outlet' => $request->outlet_id[0] ?? ''])
                ->with('success', 'Promosi berhasil ditambahkan');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Gagal simpan promo: " . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Gagal simpan data: ' . $e->getMessage());
        }
    }

    public function edit(Request $request, $id)
    {
        $promotion = ProductPromotion::with('outlets')->findOrFail($id);

        return view('poz::transaction.promotion.index', [
            'action'     => 'edit',
            'promotion'  => $promotion,
            'outlet_id'  => $request->outlet,
            'products'   => Product::orderBy('name', 'asc')->get(),
            'categories' => Category::orderBy('name', 'asc')->get(),
        ]);
    }

    public function update(Request $request, $id)
    {
        $promotion = ProductPromotion::findOrFail($id);

        $request->validate([
            'name'        => 'required|string|max:255',
            'type'        => 'required|integer',
            'start_date'  => 'required|date',
            'end_date'    => 'nullable|date|after_or_equal:start_date',
            'config'      => 'required|array'
        ]);

        DB::beginTransaction();
        try {
            $data = $request->only(['name', 'type', 'start_date', 'end_date']);

            $newConfig = $this->formatConfigWithModels($request->config, $request->type);

            if ($request->hasFile('image')) {
                if ($promotion->image_name && file_exists(public_path('uploads/' . $promotion->location . '/' . $promotion->image_name))) {
                    unlink(public_path('uploads/' . $promotion->location . '/' . $promotion->image_name));
                }

                $file = $request->file('image');
                $filename = time() . '_' . $file->getClientOriginalName();
                $location = 'promotion';
                $file->move(public_path('uploads/' . $location), $filename);

                $data['location'] = $location;
                $data['image_name'] = $filename;
            }

            $data['updated_by'] = Auth::id();

            $promotion->fill($data);
            $promotion->config = $newConfig;
            $promotion->save();

            $promotion->outlets()->sync($request->outlet_id);

            DB::afterCommit(function () use ($promotion) {
                $this->sendPromotionNotification($promotion, 'update');
            });

            DB::commit();

            return redirect()
                ->route('poz::transaction.product-promotion.index', ['outlet' => $request->outlet_id[0] ?? ''])
                ->with('success', 'Promosi berhasil diperbarui');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Gagal update promo ID {$id}: " . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Gagal perbarui data: ' . $e->getMessage());
        }
    }

    public function destroy(Request $request, $id)
    {
        $promotion = ProductPromotion::findOrFail($id);
        $promotionName = $promotion->name;

        DB::beginTransaction();
        try {
            if ($promotion->image_name && file_exists(public_path('uploads/' . $promotion->location . '/' . $promotion->image_name))) {
                unlink(public_path('uploads/' . $promotion->location . '/' . $promotion->image_name));
            }

            $promotion->delete();
            DB::afterCommit(function () use ($promotionName) {
                $details = [
                    'title'   => 'Promosi Dihapus',
                    'message' => "Promosi <strong>'{$promotionName}'</strong> telah dihapus oleh <strong>" . auth()->user()->name . "</strong>.",
                    'link'    => route('poz::transaction.product-promotion.index'),
                    'icon'    => 'bx bx-trash',
                    'color'   => 'danger'
                ];

                \Modules\Account\Models\User::broadcastSystemNotification($details);
            });

            DB::commit();
            return redirect()->back()->with('success', "Promosi berhasil dihapus");

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Gagal hapus promo: " . $e->getMessage());
            return redirect()->back()->with('error', "Gagal hapus data: " . $e->getMessage());
        }
    }

    public function promotionTable(Request $request)
    {
        $outletId = $request->outlet;
        $query = ProductPromotion::with('outlets')
            ->whereHas('outlets', function ($q) use ($outletId) {
                if ($outletId) $q->where('outlet_id', $outletId);
            });

        if ($search = $request->search) {
            $query->where('name', 'ILIKE', "%{$search}%");
        }

        if ($request->filter === 'new') {
            $query->latest();
        } elseif ($request->filter === 'old') {
            $query->oldest();
        }

        return Table::of($query)
            ->addIndexColumn()
            ->addColumn('image', function ($row) {
                $src = ($row->location && $row->image_name)
                    ? asset('uploads/' . $row->location . '/' . $row->image_name)
                    : 'https://upload.wikimedia.org/wikipedia/commons/1/14/No_Image_Available.jpg';
                return "<img width='50' height='50' class='rounded shadow-sm' src='{$src}' style='object-fit: cover;' />";
            })
            ->addColumn('action', function ($row) use ($request) {
                $outletId = $request->outlet;
                $editUrl = route('poz::transaction.product-promotion.edit', $row->id) . '?outlet=' . $outletId;
                $deleteUrl = route('poz::transaction.product-promotion.destroy', $row->id) . '?outlet=' . $outletId;

                $btn = view('poz::layouts_master.component.button_edit', ['id' => $row->id, 'update' => $editUrl])->render();
                $btn .= view('poz::layouts_master.component.button_delete', ['id' => $row->id, 'delete' => $deleteUrl])->render();

                return $btn;
            })
            ->rawColumns(['image', 'action'])
            ->make(true);
    }
}
