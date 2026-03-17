<?php

namespace Modules\Poz\Http\Livewire\Master;

use Livewire\Component;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;
use Modules\Poz\Models\Tier as TierData;
use Livewire\WithFileUploads;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Modules\Poz\Http\Requests\TierStoreRequest;
use Modules\Poz\Repositories\TierRepository;
use DB;

class Tier extends Component
{
    use WithFileUploads, TierRepository;

    public $form = [];
    public $action;

    public function mount($action, Request $req)
    {
        $id = $req->tier;
        $this->action = $action;

        if (!empty($id) && is_string($id)) {
            $this->action = 'Perbarui';
            $tier = TierData::find($id);
            $this->form['id'] = $tier->id;
            $this->form['name'] = $tier->name;
            $this->form['type'] = $tier->type;
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

                return redirect(route('poz::master.tier.index') . '?outlet=' . $outletId)->with('msg-sukses', "Data berhasil disimpan");
            } else {
                return redirect(route('poz::master.tier.index') . '?outlet=' . $outletId)->with('msg-gagal', "Data gagal disimpan");
            }
        } else if ($this->storeTier($this->form) == true) {
            return redirect(route('poz::master.tier.index') . '?outlet=' . $outletId)->with('msg-sukses', "Data berhasil disimpan");
        } else {
            return redirect(route('poz::master.tier.index') . '?outlet=' . $outletId)->with('msg-gagal', "Data gagal disimpan");
        }
    }

    public function render()
    {
        return view('poz::livewire.master.tier');
    }
}
