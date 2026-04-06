<?php

namespace Modules\Finance\Http\Controllers\Tax;

use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Modules\Core\Enums\TaxCategoryEnum;
use Modules\Core\Models\CompanyDepartment;
use Illuminate\Support\Facades\Log;
use Modules\Docs\Models\Document;
use Modules\HRMS\Enums\TaxTypeEnum;
use Modules\HRMS\Http\Controllers\Controller;
use Modules\HRMS\Models\Employee;
use Modules\HRMS\Models\EmployeeTax;
use Modules\Finance\Http\Requests\Tax\Pph\StoreRequest;
use Modules\Finance\Repositories\TaxRepository;
use Modules\HRMS\Enums\DataRecapitulationTypeEnum;
use Modules\HRMS\Models\EmployeeDataRecapitulation;
use App\Services\PayrollCalculator;
use App\Services\TerCategoryResolver;

class TerController extends Controller
{
    use TaxRepository;
    /**
     * Show the index page.
     */
    public function index(Request $request)
    {
        return view('finance::tax.ters.index', [
            'start_at'    => $start_at = Carbon::parse($request->get('start_at', cmp_cutoff(0)->format('Y-m-d')) . ' 00:00:00'),
            'end_at'      => $end_at = Carbon::parse($request->get('end_at', cmp_cutoff(1)->format('Y-m-d')) . ' 23:59:59'),
            'departments' => CompanyDepartment::visible()->with('positions')->get(),
            'types'       => collect(TaxTypeEnum::cases()),
            'employees' => Employee::with([
                'salaries' => fn($salary) => $salary->whereDate('start_at', $start_at->format('Y-m-d'))->whereDate('end_at', $end_at->format('Y-m-d')),
                'taxs' => fn($tax) => $tax->whereDate('start_at', $start_at->format('Y-m-d'))->whereDate('end_at', $end_at->format('Y-m-d')),
                'user',
                'position.position'
            ])->has('contract')->search($request->get('search'))->whenWithTrashed($request->get('trashed'))->paginate($request->get('limit', 10)),
        ]);
    }

    /**
     * Create a new resource.
     */
    public function create(Request $request)
    {
        $configs = setting('cmp_pph_objective_percentage');

        if (!$configs) {
            return redirect()->next()->with(['error' => 'Mohon setting persentase pengenaan pajak di template pph21!']);
        }

        $start_at = Carbon::parse($request->get('start_at', cmp_cutoff(0)->format('Y-m-d')) . ' 00:00:00');
        $end_at   = Carbon::parse($request->get('end_at', cmp_cutoff(1)->format('Y-m-d')) . ' 23:59:59');

        $employee = Employee::with([
            'user',
            'position.position',
            'dataRecapitulations' => fn($recaps) => $recaps
                ->where('type', DataRecapitulationTypeEnum::PPH21)
                ->whereDate('start_at', $start_at->format('Y-m-d'))
                ->whereDate('end_at', $end_at->format('Y-m-d')),
        ])->has('contract')->find($request->get('employee'));

//        $ters = getTERCategory($employee->user->getMeta('profile_mariage'), $employee->user->getMeta('profile_child'), $employee->user->getMeta('profile_sex'), $employee->getMeta('tax_status'));
        $data = $this->getComponentValue($employee, $start_at, $end_at);

        $collectionData = collect($data);
        $totalBruto = $collectionData->whereIn('ctg_az', [1, 2])->sum('real_salary');

        $terResolver = app(TerCategoryResolver::class);
        $ters = $terResolver->resolve(
            $employee->user->getMeta('profile_mariage'),
            $employee->user->getMeta('profile_child'),
            $employee->user->getMeta('profile_sex')
        );

        $calculator = app(PayrollCalculator::class);

        $context = [
            'gaji'          => $totalBruto,
            'penghasilan'   => $totalBruto,
            'mariage'       => $employee->user->getMeta('profile_mariage'),
            'child'         => $employee->user->getMeta('profile_child'),
            'sex'           => $employee->user->getMeta('profile_sex'),
            'tax_category'  => $ters['category'],
            'employee_type' => $employee->getMeta('employee_type') ?? 'monthly',
        ];

        $terNominal = $calculator->calculate('TER', $context, $start_at);

        return view('finance::tax.ters.create', [
            'start_at'   => $start_at,
            'end_at'     => $end_at,
            'types'      => [TaxTypeEnum::TER],
            'categories' => collect(TaxCategoryEnum::cases()),
            'employee'   => $employee,
            'earnings'   => collect($data)->whereIn('ctg_az', [1, 2]),
            'reductions' => collect($data)->where('ctg_az', 3),
            'ters'       => $ters,
            'configs'    => $configs,
        ]);
    }

    /**
     * Store a resource to database.
     */
    public function store(StoreRequest $request)
    {
        try {
            $pph = $this->storeTaxTer(
                $request->transformed()->toArray(),
                $request->boolean('as_recap')
            );

            $message = $request->boolean('as_recap')
                ? 'PPh 21 beserta rekap potongan penggajian <strong>' . $pph->employee->user->name . '</strong> berhasil dibuat.'
                : 'PPh 21 <strong>' . $pph->employee->user->name . '</strong> berhasil dibuat.';

            return redirect()->next()->with('success', $message);
        } catch (\Throwable $th) {
            Log::error("Failed storeTaxTer: " . $th->getMessage());

            $message = $request->boolean('as_recap')
                ? 'Terjadi kegagalan saat membuat rekap PPh21 <strong>' . $request->employee_name . '</strong>, silakan masukan ke rekap secara manual.'
                : 'Terjadi kesalahan. Silakan coba kembali.';

            return redirect()->next()->with('danger', $message);
        }
    }

    /**
     * Show a resource.
     */
    public function show(EmployeeTax $ter_tax)
    {
        return view('finance::tax.ters.show', [
            'tax'   => $ter_tax,
            'configs' => setting('cmp_pph_objective_percentage')
        ]);
    }

    /**
     * destroy a resource.
     */
    public function destroy(EmployeeTax $ter_tax)
    {
        $tmp = $ter_tax;
        if ($ter_tax->delete()) {
            $recap = EmployeeDataRecapitulation::whereType(DataRecapitulationTypeEnum::PPH21)
                ->where('start_at', $tmp->start_at->format('Y-m-d'))
                ->where('end_at', $tmp->end_at->format('Y-m-d'))
                ->where('empl_id', $tmp->empl_id)
                ->first();
            if ($recap) {
                $recap->delete();
            }
            return redirect()->next()->with('success', 'PPh 21 berhasil dihapus.');
        }
        return redirect()->fail();
    }

    /**
     * print the document to PDF.
     */
    public function print(Document $employeetax)
    {
        return $employeetax->show();
    }

    /**
     * Release the resource to employee.
     */
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
