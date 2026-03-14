<?php

namespace Modules\Cms\Http\Livewire\Builder;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

use Livewire\Component;
use Illuminate\Support\Facades\Session;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;
use Illuminate\Http\Request;
use Livewire\WithFileUploads;
use Livewire\Attributes\Validate;
use Illuminate\Support\Facades\Storage;
use Modules\Cms\Models\CmsPost;
use Modules\Cms\Models\CmsMenu;
use Modules\Cms\Models\CmsMenuRelated;
use Cookie;
use Redirect;
use DB;


class Posting extends Component
{
    use AuthorizesRequests;

    // use LivewireAlert;
    use WithFileUploads;

    public $data_id;
    public $title = '';
    public $content = [];
    public $countsdt;
    public $cookie;
    public $language = 'id';
    public $menu_id;
    public $image_desc;
    public $medias = '';
    public $dropify;
    public $tags;
    public $stt;
    public $sh_photo;
    public $category = [];
    public $contentpost;
    public $manyArr = [];
    public $allForm = [];
    public $selectFormAdd = '';
    public $postId = '';

    #[Validate('image|max:1024')] // 1MB Max
    public $photo;

    public function mount(Request $req)
    {
        $id = strtok($req->post_id, '/');
        $this->postId = preg_replace('/\D/', '', $req->post_id);
        $this->menu_id = $req->id_menu;
        $this->stt = CmsMenu::where('id', $this->menu_id)->first()->type;
        $pst_code = CmsMenu::where('id', $this->menu_id)->first();
        $this->medias = json_decode($pst_code->image_code);
    
        $this->countsdt = [];

        $menuRelated = CmsMenuRelated::where('from_menu', $req->id_menu)->get();
        foreach ($menuRelated as $rel => $varel) {
            $this->manyArr[$varel->with_menu] = $varel->with_menu;
        }

        // Ambil cookie bahasa jika tersedia
        if (isset($_COOKIE['_x_language'])) {
            $this->language = trim($_COOKIE['_x_language'], '"');
        }

        if (!empty($id) && is_string($id)) {
            if (count((array) json_decode($pst_code->post_code)) > 0) {
                foreach (json_decode($pst_code->post_code, true) as $index_pc => $value_pc) {
                    $this->countsdt[] = 'post' . count($this->countsdt);
                }
            }

            $this->data_id = $id;
            $data_to_post = CmsPost::findOrFail($id);
            $pst = CmsPost::with('categories')->findOrFail($id);
            $category_to_post = $pst->categories->toArray();

            $this->sh_photo = $data_to_post->location . '/' . $data_to_post->image;
            $this->tags = $data_to_post->tags;
            $this->image_desc = @$data_to_post->alt_image;

            $titles = 'title';
            $imagez = 'media_description';
            $metasd = 'meta_description';
            array_push($this->countsdt, $titles, $imagez, $metasd);

            $arr = [];
            foreach ($this->countsdt as $field) {
                $arr[$field] = '';
            }

            // Decode konten dari JSON ke dalam array terstruktur
            $content_array = json_decode($data_to_post->content, true);
            $merged = [];

            foreach ($content_array as $lang => $fields) {
                foreach ($this->countsdt as $field) {
                    $merged[$lang][$field] = $fields[$field] ?? '';
                }
            }

            // Simpan ke session jika belum ada
            if (!$req->session()->has("posting")) {
                $req->session()->put("posting", $merged);
            }

            // Isi ke properti Livewire jika ada session sesuai bahasa
            if (isset($merged[$this->language])) {
                $this->content = $merged[$this->language];

                $helper_arr = [];
                foreach ($this->content as $key => $val) {
                    $helper_arr[str_replace('post', '', $key)] = $val;
                }

                $this->dispatch('helper', $helper_arr);
            }

            foreach ($category_to_post as $val) {
                $this->category[$val['parameter']] = $val['tags_id'];
            }

            $this->menu_id = $data_to_post->menu_id;
        } else {
            // Untuk kondisi create baru
            if (count((array) json_decode($pst_code->post_code)) > 0) {
                foreach (json_decode($pst_code->post_code, true) as $index_pc => $value_pc) {
                    $this->countsdt[] = 'post' . count($this->countsdt);
                }
            }

            $titles = 'title';
            $imagez = 'media_description';
            $metasd = 'meta_description';
            array_push($this->countsdt, $titles, $imagez, $metasd);

            $arr = [];
            foreach ($this->countsdt as $field) {
                $arr[$field] = '';
            }

            $merged = [$this->language => $arr];
            $req->session()->put("posting", $merged);
            $this->content = $merged[$this->language];

            // Cek jika cookie konten tersedia
            if (isset($_COOKIE['_x_content'])) {
                $contents = json_decode($_COOKIE['_x_content'], true);
                if (is_array($contents)) {
                    $this->cookie = $contents;
                }
            }
        }
    }

    public function clearSession(Request $request)
    {
        $request->session()->forget('posting.id');
        //d($request->session()->get("posting"));
        $titles = 'title';
        $imagez = 'media_description';
        $metasd = 'meta_description';

        array_push($this->countsdt, $titles);
        array_push($this->countsdt, $imagez);
        array_push($this->countsdt, $metasd);

        $arr = [];
        foreach ($this->countsdt as $key => $val) {
            $arr[$val] = '';
        }

        $request->session()->put("posting." . 'id', $arr);
    }

    public function selectForm($event) {}

    public function moneys($money, $name, Request $request)
    {
        $exp = explode('.', $name);
        $this->{$exp[0]}[$exp[1]] = number_format(round($money, 0), 0, ",", ".");
        $request->session()->put("posting." . $this->language . '.' . $exp[1], $this->{$exp[0]}[$exp[1]]);
    }

    public function selectlang($event, Request $request)
    {
        $this->language = $event;

        if (!isset($request->session()->get("posting")[$this->language])) {

            $titles = 'title';
            $imagez = 'media_description';
            $metasd = 'meta_description';

            array_push($this->countsdt, $titles);
            array_push($this->countsdt, $imagez);
            array_push($this->countsdt, $metasd);

            $arr = [];
            foreach ($this->countsdt as $key => $val) {
                $arr[$val] = '';
            }


            $request->session()->put("posting." . $this->language, $arr);
            $this->dispatch('helper', $arr);
        } else {
            $arr = [];
            foreach ($request->session()->get("posting")[$this->language] as $key => $val) {
                if (isset($this->content[$key])) {
                    $this->content[$key] = $val;
                    $arr[str_replace('post', '', $key)] = $val;
                }
            }

            $this->dispatch('helper', $arr);
        }
    }

    public function helperlanguage($event, Request $request)
    {
        if ($event !== 'kosong') {
            if (is_countable($request->session()->get("posting")) && count($request->session()->get("posting"))) {
                $request->session()->get("posting")[$this->language] = $this->content;

                if (isset($request->session()->get("posting")[$this->language])) {
                    foreach ($request->session()->get("posting")[$this->language] as $key => $val) {
                        if (isset($this->content[$key])) {
                            $request->session()->put("posting." . $this->language . '.' . $key, $this->content[$key]);
                        }
                    }
                }
            } else {

                $request->session()->put("posting." . $this->language, $this->content);
            }
        }


        //$this->dispatch('helper', $event);
    }

    public function checkSession(Request $request)
    {
        dd($request->session()->get("posting"));
    }

   public function submitForm(Request $request)
    {
        try {
            $location = 'image_posting/' . $this->menu_id . '/' . uniqid();

            if ($this->data_id) {
                $post = CmsPost::find($this->data_id);
            } else {
                $post = new CmsPost();
            }

            $arr = [];

            // Perulangan multibahasa
           
            // foreach ($this->content as $lang => $fields) {
                foreach ($this->content as $field => $value) {
                    if ($field === 'title') {
                        $arr['id']['slug'] = strtolower(str_replace(' ', '-', $value));
                    }
                    $arr['id'][$field] = $value;
                }
            //}



            $post->alt_image = $this->image_desc;
            $post->content = json_encode($arr);
            $post->menu_id = $this->menu_id;

            if (!empty($this->dropify)) {
                $post->image = $this->dropify->getFilename();
                $post->location = $location;
            }

            $post->tags = $this->tags;

            if (!empty($this->dropify)) {
                $this->dropify->storeAs($location, $this->dropify->getFilename(), 'public');
            }

            if ($this->data_id) {
                $post->updated_by = \Auth::user()->id;
                $post->save();

                $pst = CmsPost::findOrFail($this->data_id);
                $pst->categories()->detach();

                foreach ($this->category as $index => $value) {
                    if (!empty($value)) {
                        $pst->categories()->attach(
                            $value,
                            ['post_id' => $this->data_id, 'tags_id' => $value, 'parameter' => $index]
                        );
                    }
                }
            } else {
                $post->created_by = \Auth::user()->id;
                $post->updated_by = \Auth::user()->id;
                $post->status = $this->stt;

                // Simpan title ke meta jika type == 4
                if ($this->stt == 4 && isset($arr[$this->language]['title'])) {
                    $post->setMeta('content', $arr[$this->language]['title']);
                }

                $post->save();

                foreach ($this->category as $index => $value) {
                    if (!empty($value)) {
                        $post->categories()->attach(
                            $value,
                            ['post_id' => $post->id, 'tags_id' => $value, 'parameter' => $index]
                        );
                    }
                }
            }

            $url = \URL::route('cms::builder.posting.index') . "?id_menu=" . $this->menu_id;
            return redirect($url)->with('msg', "Data berhasil disimpan");
        } catch (\Exception $e) {
            dd($e->getMessage());
        }
    }


    protected function cleanupOldUploads()
    {

        $storage = Storage::disk('local');

        foreach ($storage->allFiles('livewire-tmp') as $filePathname) {
            // On busy websites, this cleanup code can run in multiple threads causing part of the output
            // of allFiles() to have already been deleted by another thread.
            if (! $storage->exists($filePathname)) continue;

            $yesterdaysStamp = now()->subSeconds(4)->timestamp;
            if ($yesterdaysStamp > $storage->lastModified($filePathname)) {
                $storage->delete($filePathname);
            }
        }
    }

    public function render()
    {
        $data = CmsMenu::where('id', $this->menu_id)->first();

        return view('cms::livewire.builder.posting', ['data' => $data]);
    }
}
