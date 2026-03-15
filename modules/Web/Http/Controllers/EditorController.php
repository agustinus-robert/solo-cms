<?php

namespace Modules\Web\Http\Controllers;

use Illuminate\Http\Request;
use Modules\Account\Models\User;
use Modules\Account\Repositories\UserRepository;
use Modules\Web\Http\Controllers\Controller;
use Modules\Admin\Http\Requests\Teacher\StoreRequest;
use Modules\Admin\Http\Requests\Teacher\UpdateRequest;
use Modules\Course\Models\Teacher;

class EditorController extends Controller
{
    public function edit($editor_sidebar, Request $request)
    {
        $post_id = $request->query('post_id') ?? null;
        $posting = null;

        if(!empty($post_id)){
            $post_id = decrypt($request->query('post_id'));
        }

        $data = get_data_by_menu($editor_sidebar, $post_id, true);
        $menu = \DB::table('cms_menu')->where('id', $editor_sidebar)->first();

        if (!$data || !$menu) {
            return "<div class='alert alert-danger'>Data tidak ditemukan.</div>";
        }

        $content = get_content_json($data);
        $post_codes = json_decode($menu->post_code, true) ?? [];

        if(!empty($post_id)){
            $posting = \DB::table('cms_post')->where('id', $post_id)->first();
        }

        return view('web::layouts.components.cms-form-builder', [
            'data' => $data,
            'content' => $content,
            'id_menu' => $editor_sidebar,
            'post_id' => $post_id,
            'post_codes' => $post_codes,
            'posting' => $posting
        ]);
    }

    public function update(Request $request)
    {
        $id_menu = $request->input('id_menu');
        $encrypted_post_id = $request->input('post_id');
        $post_id = null;

        if ($encrypted_post_id) {
            try {
                $post_id = \Crypt::decrypt($encrypted_post_id);
            } catch (\Exception $e) {
                return response("ID tidak valid", 403);
            }
        }

        $newFields = $request->except(['_token', '_method', 'id_menu', 'post_id', 'image']);
        if (isset($newFields['title'])) {
            $newFields['slug'] = \Str::slug($newFields['title']);
        }

        $finalContent = [
            'id' => $newFields
        ];

        try {
            $post = \DB::table('cms_post')->where('id', $post_id)->first();

            $dataUpdate = [
                'menu_id' => $id_menu,
                'content' => json_encode($finalContent),
                'updated_at' => now(),
            ];

            if ($request->hasFile('image')) {
                $file = $request->file('image');
                $filename = $file->getClientOriginalName();
                $location = 'image_posting/' . $id_menu . '/' . uniqid();

                $file->storeAs($location, $filename, 'public');

                $dataUpdate['image'] = $filename;
                $dataUpdate['location'] = $location;
            }

            if ($post) {
                \DB::table('cms_post')->where('id', $post_id)->update($dataUpdate);
            } else {
                $dataUpdate['created_at'] = now();
                $menu = \DB::table('cms_menu')->where('id', $id_menu)->first();
                $dataUpdate['status'] = $menu->type ?? 1;

                \DB::table('cms_post')->insert($dataUpdate);
            }

            return response("")->header('HX-Trigger', 'refreshSection');

        } catch (\Exception $e) {
            return response("Gagal: " . $e->getMessage(), 500);
        }
    }
}
