<?php

namespace Modules\Core\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Core\Models\CompanyPositionType;

class PositionTypeController extends Controller
{
    public function index()
    {
        $items = CompanyPositionType::latest()->paginate(10);
        return view('core::company.positiontype.index', compact('items'));
    }

    public function create()
    {
        return view('core::company.positiontype.upsert', [
            'item' => new CompanyPositionType(),
            'title' => 'Create Position Type'
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kd'        => 'required|unique:cmp_position_types,kd',
            'name'      => 'required|string|max:255',
            'is_active' => 'boolean',
            'meta'      => 'nullable|array',
        ]);

        CompanyPositionType::create($validated);

        return redirect()->route('core::company.position-type.index')->with('success', 'Data created successfully');
    }

    public function edit($id)
    {
        $item = CompanyPositionType::findOrFail($id);

        return view('core::company.positiontype.upsert', [
            'item' => $item,
            'title' => 'Edit Position Type: ' . $item->name
        ]);
    }

    public function update(Request $request, $id)
    {
        $item = CompanyPositionType::findOrFail($id);

        $validated = $request->validate([
            'kd'        => 'required|unique:cmp_position_types,kd,' . $id,
            'name'      => 'required|string|max:255',
            'is_active' => 'boolean',
            'meta'      => 'nullable|array',
        ]);

        $item->update($validated);

        return redirect()->route('core::company.position-type.index')->with('success', 'Data updated successfully');
    }

    public function destroy($id)
    {
        CompanyPositionType::findOrFail($id)->delete();
        return redirect()->route('core::company.position-type.index')->with('success', 'Data deleted successfully');
    }
}
