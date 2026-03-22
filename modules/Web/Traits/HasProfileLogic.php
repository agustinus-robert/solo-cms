<?php

namespace Modules\Web\Traits;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Modules\Account\Models\UserAddress;

trait HasProfileLogic
{
    public function performProfileUpdate(Request $request)
    {
        $user = Auth::user();

        DB::beginTransaction();
        try {
            $user->update(['name' => $request->name]);

            $profileData = $request->only(['name', 'phone', 'pob', 'dob', 'sex']);

            if ($request->hasFile('avatar')) {
                if ($user->profile && $user->profile->avatar) {
                    Storage::disk('public')->delete($user->profile->avatar);
                }
                $profileData['avatar'] = $request->file('avatar')->store('avatars', 'public');
            }

            $user->profile()->updateOrCreate(['user_id' => $user->id], $profileData);

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
