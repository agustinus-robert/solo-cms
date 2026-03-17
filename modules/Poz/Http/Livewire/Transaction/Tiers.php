<?php

namespace Modules\Poz\Http\Livewire\Transaction;

use Livewire\Component;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;
use Modules\Poz\Models\Tier as TierRef;
use Modules\Poz\Models\TierTransaction as TierData;
use Livewire\WithFileUploads;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Modules\Poz\Http\Requests\TierStoreRequest;
use Modules\Poz\Repositories\TierTransactionRepository;
use DB;

class Tiers extends Component
{
    use WithFileUploads, TierTransactionRepository;

    public $form = [];
    public $tiers = [];
    public $action;

    public function mount($action, Request $req)
    {
        $id = $req->tier_variant;
        $outletId = $req->outlet;

        $this->action = $action;
        $this->tiers = TierRef::with('user', 'outlets')
            ->whereNull('deleted_at')
            ->whereHas('outlets', function ($query) use ($outletId) {
                $query->where('outlet_id', $outletId);
            })->get();

        if (!empty($id) && is_string($id)) {
            $this->action = 'Perbarui';
            $tier = TierData::find($id);
            $this->form['id'] = $tier->id;
            $this->form['name'] = $tier->name;
            $this->form['ref_tier_id'] = $tier->ref_tier_id;
        } else {
            $this->action = 'Tambah';
            $digits = '0123456789';
            $randomNumbers = substr(str_shuffle(str_repeat($digits, 10)), 0, 10);
            $this->form['code'] = $randomNumbers;
        }

        $this->form['outlet'] = request()->query('outlet', auth()->user()->current_outlet_id);
    }

    protected function rules()
    {
        $rules = (new TierStoreRequest())->rules();
        return $rules;
    }

    protected function attributes()
    {
        $attrs = (new TierStoreRequest())->attributes();
        return $attrs;
    }

    protected function messages()
    {

        $message = (new TierStoreRequest())->messages();
        return $message;
    }

    public function save()
    {
        $this->validate(
            $this->rules(),
            $this->messages(),
            $this->attributes()
        );

        $outletId = $this->form['outlet'];

        if (isset($this->form['id']) && !empty($this->form['id'])) {
            if ($this->updateTier($this->form, $this->form['id']) == true) {

                return redirect(route('poz::transaction.tier-variant.index') . '?outlet=' . $outletId)->with('msg-sukses', "Data berhasil disimpan");
            } else {
                return redirect(route('poz::transaction.tier-variant.index') . '?outlet=' . $outletId)->with('msg-gagal', "Data gagal disimpan");
            }
        } else if ($this->storeTier($this->form) == true) {
            return redirect(route('poz::transaction.tier-variant.index') . '?outlet=' . $outletId)->with('msg-sukses', "Data berhasil disimpan");
        } else {
            return redirect(route('poz::transaction.tier-variant.index') . '?outlet=' . $outletId)->with('msg-gagal', "Data gagal disimpan");
        }
    }

    public function render()
    {
        return view('poz::livewire.transaction.tier');
    }
}
