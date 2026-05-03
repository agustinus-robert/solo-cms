<?php

namespace Modules\Tour\Http\Controllers\Transaction;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Modules\Tour\Models\Tour;
use Modules\Tour\Models\TourPhoto;

class TourPhotoController extends Controller
{
    public function show(Tour $tour)
    {
        $photos = $tour->photos;
        return view('tour::documentation.photo', compact('tour', 'photos'));
    }

    public function store(Request $request, Tour $tour)
    {
        $validator = Validator::make($request->all(), [
            'photos.*' => 'required|image|mimes:jpeg,png,jpg|max:2048'
        ], [
            'photos.*.image' => 'File harus berupa gambar.',
            'photos.*.max'   => 'Ukuran foto maksimal 2MB.',
        ]);

        if ($validator->fails()) {
            return back()->withErrors(['error' => 'Validasi gagal: Pastikan file berupa gambar (JPG, PNG) dan maksimal 2MB.'])->withInput();
        }

        try {
            if ($request->hasFile('photos')) {
                foreach ($request->file('photos') as $file) {
                    $fileName = $file->hashName();
                    $file->move(public_path('tours'), $fileName);

                    TourPhoto::create([
                        'tour_id' => $tour->id,
                        'image_path' => 'tours/' . $fileName,
                        'is_primary' => $tour->photos()->count() == 0
                    ]);
                }
            }
            return back()->with('success', 'Foto berhasil diunggah.');

        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Gagal mengunggah foto: ' . $e->getMessage()]);
        }
    }

    public function setPrimary(Tour $tour, TourPhoto $photo)
    {
        $tour->photos()->update(['is_primary' => false]);
        $photo->update(['is_primary' => true]);

        return back()->with('success', 'Foto utama berhasil diatur.');
    }

    public function destroy(TourPhoto $photo)
    {
        Storage::disk('public')->delete($photo->image_path);
        $photo->delete();

        return back()->with('success', 'Foto berhasil dihapus.');
    }
}
