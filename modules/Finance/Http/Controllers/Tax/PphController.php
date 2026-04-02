<?php

namespace Modules\Finance\Http\Controllers\Tax;

use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Modules\Core\Enums\TaxCategoryEnum;
use Modules\Core\Models\CompanyDepartment;
use Modules\HRMS\Enums\TaxTypeEnum;
use Modules\HRMS\Http\Controllers\Controller;
use Modules\HRMS\Models\Employee;
use Modules\HRMS\Models\EmployeeTax;
use Modules\Finance\Http\Requests\Tax\Pph\StoreRequest;
use Modules\Finance\Repositories\TaxYearlyRepository;

class PphController extends Controller
{
    use TaxYearlyRepository;
    /**
     * Show the index page.
     */
    public function index(Request $request)
    {
        return view('finance::tax.pph.index', [
            'start_at'    => $start_at = Carbon::parse($request->get('start_at', now()->startOfYear()->format('Y-m-d')) . ' 00:00:00'),
            'end_at'      => $end_at = Carbon::parse($request->get('end_at', now()->endOfYear()->format('Y-m-d')) . ' 23:59:59'),
            'departments' => CompanyDepartment::visible()->with('positions')->get(),
            'types'       => collect(TaxTypeEnum::cases()),
            'employees' => Employee::with([
                'taxs' => fn($tax) => $tax->where('type', TaxTypeEnum::YEARLY)->whereDate('start_at', $start_at->format('Y-m-d'))->whereDate('end_at', $end_at->format('Y-m-d')),
                'user',
                'position.position'
            ])
                ->has('contract')
                ->search($request->get('search'))
                ->whenWithTrashed($request->get('trashed'))
                ->paginate($request->get('limit', 10)),
        ]);
    }


    public function create(Request $request)
    {
        $defaults = setting('cmp_pph_components');

        if (!$defaults) {
            return redirect()->fail('Harap membuat template pajak tahunan terlebih dahulu!');
        }

        if (!$request->filled(['start_at', 'end_at'])) {
           return redirect()->back()->with('fail', 'Harap tambahkan periode tahunan');
        }

        $start_at = Carbon::parse($request->get('start_at', cmp_cutoff(0)->format('Y-m-d')) . ' 00:00:00');
        $end_at   = Carbon::parse($request->get('end_at', cmp_cutoff(1)->format('Y-m-d')) . ' 23:59:59');


        $employee = Employee::with([
            'salaryTemplates.items.component',
            'user',
            'position.position'
        ])->has('contract')->find($request->get('empl_id'));

        $months = $employee->joined_at->diffInMonths(now())  <= 12 ? $employee->joined_at->diffInMonths(now()) : 12;
        $is_npwp = $employee->user->getMeta('tax_number') ? true : false;

        $ptkp = $this->getPtkp($employee->user);
        $data = $this->getYearComponentValue($employee, $start_at, $end_at, $months);

        return view('finance::tax.pph.create', [
            'start_at'   => $start_at,
            'end_at'     => $end_at,
            'types'      => [TaxTypeEnum::YEARLY],
            'categories' => collect(TaxCategoryEnum::cases()),
            'employee'   => $employee,
            'earnings'   => collect($data)->whereIn('ctg_az', [1, 2]),
            'reductions' => collect($data)->where('ctg_az', 3),
            'ptkp'       => $ptkp,
            'months'     => $months,
            'is_npwp'    => $is_npwp,
        ]);
    }

    public function store(StoreRequest $request)
    {
        $pph = $request->transformed()->toArray();

        if ($empl = $this->storeTaxYear($pph, true)) {
            return redirect()->next()->with('success', 'PPh 21 tahunan <strong>' . $empl->employee->user->name . '</strong> berhasil dibuat.');
        }
        return redirect()->fail();
    }

    public function show(EmployeeTax $tax)
    {
        return view('finance::tax.pph.show', [
            'categories' => collect(TaxCategoryEnum::cases()),
            'is_npwp' => $tax->employee->user->getMeta('tax_number') ? true : false,
            'tax'   => $tax
        ]);
    }

    public function destroy(EmployeeTax $tax)
    {
        $tmp = $tax;
        if ($tax->delete()) {
            return redirect()->next()->with('success', 'PPh 21 tahunan karywan <strong>' . $tmp->employee->user->name . '</strong> berhasil dihapus!');
        }
        return redirect()->fail();
    }

    public function release(Request $request)
    {
        $start_at = Carbon::parse($request->get('start_at', cmp_cutoff(0)->format('Y-m-d')) . ' 00:00:00');
        $end_at   = Carbon::parse($request->get('end_at', cmp_cutoff(1)->format('Y-m-d')) . ' 23:59:59');

        if (EmployeeTax::where('type', TaxTypeEnum::MONTHLY)->whenPeriod($start_at, $end_at)->update(['released_at' => now()])) {
            return redirect()->next()->with('success', 'PPh 21 berhasil diterbitkan.');
        }
        return redirect()->fail();
    }

    public function summary(Request $request)
    {
        $start_at = Carbon::parse($request->get('start_at', cmp_cutoff(0)->format('Y-m-d')) . ' 00:00:00');
        $end_at = Carbon::parse($request->get('end_at', cmp_cutoff(1)->format('Y-m-d')) . ' 23:59:59');

        $taxs = EmployeeTax::with([
            'employee' => fn($w) => $w->with('user', 'contract', 'position.position')
        ])->whereType(TaxTypeEnum::MONTHLY)->whereStartAt($start_at)->whereEndAt($end_at)->get();

        $sheet['Penghitungan PPh 21'] = [
            'columns' => [
                'number' => 'No',
                'name' => 'Nama Karyawan',
                'department' => 'Departemen',
                'position' => 'Posisi',
                'start_at' => 'Periode mulai',
                'end_at' => 'Periode selesai',
                'type' => 'Kategori',
                'bruto_month' => 'Bruto 1 bulan',
                'bruto_year' => 'Bruto 1 tahun',
                'reduction_month' => 'Potongan 1 bulan',
                'reduction_year' => 'Potongan 1 tahun',
                'charge' => 'Biaya jabatan',
                'netto' => 'Pendapatan bersih',
                'ptkp' => 'PTKP',
                'pkp' => 'PKP',
                'pph1' => 'PPh lapis pertama',
                'pph2' => 'PPh lapis kedua',
                'pph3' => 'PPh lapis ketiga',
                'pph4' => 'PPh lapis keempat',
                'pph5' => 'PPh lapis kelima',
                'pph_total' => 'Total PPh',
            ],
            'data' => $taxs->map(function ($item, $index) {
                return [
                    'number'        => $index + 1 ?? '',
                    'name'          => $item->employee->user->name ?? 0,
                    'department'    => $item->employee->position->position->department->name ?? 0,
                    'position'      => $item->employee->position->position->name ?? 0,
                    'start_at'      => Carbon::parse($item->start_at)->isoFormat('LL') ?? 0,
                    'end_at'        => Carbon::parse($item->end_at)->isoFormat('LL') ?? 0,
                    'type'          => $item->type->label() ?? 0,
                    'bruto_month'   => (int) ($item->meta?->income->totalmonth ?? 0),
                    'bruto_year'    => (int) ($item->meta?->income->totalyear ?? 0),
                    'reduction_month' => (int) ($item->meta?->reduction->totalmonth ?? 0),
                    'reduction_year'  => (int) ($item->meta?->reduction->totalyear ?? 0),
                    'charge'        => (int) ($item->meta?->charge ?? 0),
                    'netto'         => (int) ($item->meta?->netto ?? 0),
                    'ptkp'          => (int) ($item->meta?->ptkp ?? 0),
                    'pkp'           => (int) ($item->meta?->pkp ?? 0),
                    'pph1'          => (int) ($item->meta?->pph1 ?? 0),
                    'pph2'          => (int) ($item->meta?->pph2 ?? 0),
                    'pph3'          => (int) ($item->meta?->pph3 ?? 0),
                    'pph4'          => (int) ($item->meta?->pph4 ?? 0),
                    'pph5'          => (int) ($item->meta?->pph5 ?? 0),
                    'pph_total'     => (int) ($item->meta?->pphtotal ?? 0),
                ];
            }),
        ];

        return response()->json([
            'title'     => ($title = 'Rekap PPh21 periode ' . date('Y-m-d', strtotime($start_at)) . ' - ' . date('Y-m-d', strtotime($end_at))),
            'subtitle'  => 'Diunduh pada ' . now()->isoFormat('LLLL'),
            'file'      => Str::slug($title . '-' . time()),
            'sheets'    => $sheet
        ]);
    }
}
