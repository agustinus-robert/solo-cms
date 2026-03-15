<?php

namespace Modules\Web\Http\Controllers;

use App\Http\Controllers\Controller as AppController;
use Modules\Cms\Models\CmsLiveEditorsAccess;
use Illuminate\Support\Facades\Auth;
use Modules\Web\Traits\HasSectionsTraits;
use App\Models\Setting;
use Illuminate\Support\Facades\View;

class Controller extends AppController {
    use HasSectionsTraits;

    public function __construct()
    {
        $theme = Setting::where('key', 'theme')->first();

        view()->composer('web::'.$theme->value.'.index', function ($view) {
            $canEdit = false;

            if (Auth::check()) {
                $access = CmsLiveEditorsAccess::where('user_id', Auth::id())
                            ->where('status', 1)
                            ->first();


                if ($access && request()->query('live_editor') === 'true') {
                    $canEdit = true;
                }
            }

            $view->with('canEdit', $canEdit);
            $view->with('pages', request()->path());
        });
    }
}
