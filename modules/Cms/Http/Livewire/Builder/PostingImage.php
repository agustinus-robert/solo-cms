<?php

namespace Modules\Cms\Http\Livewire\Builder;

use Livewire\Component;
use Illuminate\Support\Facades\Session;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;
use Illuminate\Http\Request;
use Livewire\WithFileUploads;
use Modules\Cms\Models\CmsPostImage;
use Modules\Cms\Models\CmsMenu;
use Livewire\Attributes\Validate;
use Illuminate\Support\Facades\Storage;
use Redirect;
use DB;


class PostingImage extends Component
{
    // use LivewireAlert;
    use WithFileUploads;

    public $data_id;
    public $title;
    public $content = '';
    public $menu_id;
    public $photo;
    public $post_id;
    public $tags;
    public $sh_photo;
    public $category;
    public $dropify;


    public function mount(Request $req)
    {
        $this->menu_id = $req->id_menu;
        $this->post_id = $req->post_id;
        $id = $req->id;

        if (!empty($id) && is_string($id)) {
            $this->data_id = $id;
            $data_to_post = CmsPostImage::where('id', $id)->first();
            $postImage = CmsPostImage::with('categories')->findOrFail($id);
            $category_to_post = $postImage->categories;

            $this->title = $data_to_post->title;
            $this->sh_photo = $data_to_post->location . '/' . $data_to_post->image;
            if (isset($category_to_post->id_category_image)) {
                $this->category = $category_to_post->id_category_image;
            }

            $this->content = $data_to_post->content;
        }
    }

    public function submitForm()
    {
        $location = 'image_posting/' . $this->menu_id . '/' . $this->post_id . '/' . uniqid();

        $arr_save = [
            'title' => $this->title,
            'slug' => strtolower(str_replace(' ', '-', $this->title)),
            'content' => json_encode($this->content),
            'menu_id' => $this->menu_id,
            'post_id' => $this->post_id
        ];

        if (!empty($this->dropify)) {
            $arr_save['location'] = $location;
            $arr_save['image'] = $this->dropify->getFilename();
            $this->dropify->storeAs($location, $this->dropify->getFilename(), 'public');
        }

        $arr_save_category = ['id_category_image' => 1];

        if ($this->data_id) {
            $postImage = CmsPostImage::findOrFail($this->data_id);
            $postImage->update($arr_save);

            $postImage->categories()->sync([1 => $arr_save_category]);
        } else {
            $hexas = hexdec(uniqid());
            $arr_save['id'] = $hexas;

            $postImage = CmsPostImage::create($arr_save);
            $postImage->categories()->attach(1, $arr_save_category);
        }

        $url = route('cms::builder.posting_image.index') . '?id_menu=' . $this->menu_id . '&post_id=' . $this->post_id;
        return redirect($url)->with('msg', "Data Photo berhasil disimpan");
    }


    public function render()
    {
        $data = CmsMenu::where('id', $this->menu_id)->first();
        return view('cms::livewire.builder.posting-image', ['data' => $data]);
    }
}
