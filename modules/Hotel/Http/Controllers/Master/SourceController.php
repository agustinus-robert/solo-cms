<?php

namespace Modules\Hotel\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Hotel\Models\BookingSource;
use Modules\Hotel\Http\Requests\Source\StoreRequest;
use Modules\Hotel\Http\Requests\Source\UpdateRequest;

use Modules\Hotel\Repositories\Master\SourceRepositories;

class SourceController extends Controller
{
    use SourceRepositories;

    public function index(Request $request)
    {
        $sources = BookingSource::latest()->get();

        if ($request->ajax()) {
            return view('hotel::source._table', compact('sources'))->render();
        }

        return view('hotel::source.index', compact('sources'));
    }

    public function create()
    {
        $source = null;
        return view('hotel::source.upsert', compact('source'));
    }

    public function store(StoreRequest $request)
    {
        try {
            $this->upsertSource($request->transform());
            return redirect()->route('hotel::source.index')
                ->with('success', 'Sumber reservasi berhasil ditambahkan.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal: ' . $e->getMessage());
        }
    }

    public function edit(BookingSource $source)
    {
        return view('hotel::source.upsert', compact('source'));
    }

    public function update(UpdateRequest $request, BookingSource $source)
    {
        try {
            $this->upsertSource($request->transform(), $source->id);
            return redirect()->route('hotel::source.index')
                ->with('success', 'Sumber reservasi berhasil diperbarui.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal: ' . $e->getMessage());
        }
    }

    public function destroy(int $id)
    {
        try {
            $this->deleteSource($id);
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
