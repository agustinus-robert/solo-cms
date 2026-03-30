@if(!empty($transfer))
    <div class="row mb-3">
        <div class="col-xl-12">
            <h6 class="fw-bold">Rate Penugasan</h6>
            
            <div class="row align-items-center mb-2">
                <label class="col-form-label col-md-4" for="">Rate Dijalani</label>
                <div class="col-md-7">
                    <div class="input-group">
                        <input class="form-control" type="number" min="0" step="0.1" name="teach[amount_jobtotal_rate]" value="">
                        <div class="input-group-text">Total</div>
                    </div>
                </div>
                <i>Rate Target Penugasan: {{$transfer->rate ?? ''}}</i>
            </div>

            <div class="row align-items-center mb-2">
                <label class="col-form-label col-md-4" for="">Jam Penugasan Regular</label>
                <div class="col-md-7">
                    <div class="input-group">
                        <input class="form-control" type="number" min="0" step="0.1" name="teach[amount_jobrate_regular]" value="">
                        <div class="input-group-text">Jam</div>
                    </div>
                </div>
                <i>Rate Per Jam Penugasan: {{$transfer->hour_rate ?? ''}}</i>
            </div>

            <div class="row align-items-center mb-2">
                <label class="col-form-label col-md-4" for="">Jam Penugasan Kelebihan</label>
                <div class="col-md-7">
                    <div class="input-group">
                        <input class="form-control" type="number" min="0" step="0.1" name="teach[amount_jobrate_overhour]" value="">
                        <div class="input-group-text">Jam</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif  