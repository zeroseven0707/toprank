<?php

namespace App\Http\Controllers;

use App\Models\City;
use App\Models\Content;
use App\Models\ContentCategory;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ContentController extends Controller
{
public function index(Request $request)
{
    $period = $request->get('period');
    $category = $request->get('category');
    $city = $request->get('city');

    $contents = Content::with(['category', 'city'])
        ->when($period, function ($q) use ($period) {
            [$year, $month] = explode('-', $period);
            $q->whereYear('created_at', $year)->whereMonth('created_at', $month);
        })
        ->when($category, fn($q) => $q->where('category_content_id', $category))
        ->when($city, fn($q) => $q->where('city', $city))
        ->paginate(10);

    return inertia('Content/Index', [
        'contents' => $contents,
        'period' => $period,
        'categories' => ContentCategory::select('id', 'name')->get(),
        'cities' => City::select('id', 'name')->get(),
        'filters' => compact('period', 'category', 'city'),
    ]);
}

    /**
     * Form tambah / edit konten
     */
    public function create(Content $content = null)
    {
        $categories = ContentCategory::select('id', 'name')->get();
        $cities = City::select('id', 'name')->get();

        return Inertia::render('Content/Form', [
            'content' => $content,
            'categories' => $categories,
            'cities' => $cities,
            'isEdit' => $content && $content->exists,
        ]);
    }

    public function publish()
    {
        $period = now()->format('Y-m');
        Content::where('period', $period)->update(['published' => true]);
        return redirect()->route('content.index')->with('success', 'Konten berhasil di publish.');
    }

    /**
     * Simpan konten baru
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category_content_id' => 'required|exists:content_categories,id',
            'city' => 'required|exists:cities,id',
            'maps_link' => 'required|string',
            'image' => 'nullable|string',
            'description' => 'nullable|string',
            'rank' => ['required', 'integer', 'min:1', \Illuminate\Validation\Rule::unique('contents')->where(fn($q) => $q->where('category_content_id', $request->category_content_id))],
        ]);

        Content::create($validated);

        return redirect()->route('content.index')->with('success', 'Konten berhasil disimpan.');
    }

    /**
     * Update konten
     */
    public function update(Request $request, Content $content)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category_content_id' => 'required|exists:content_categories,id',
            'city' => 'required|exists:cities,id',
            'maps_link' => 'required|string',
            'image' => 'nullable|string',
            'description' => 'nullable|string',
            'rank' => ['required', 'integer', 'min:1', \Illuminate\Validation\Rule::unique('contents')->ignore($content->id)->where(fn($q) => $q->where('category_content_id', $request->category_content_id))],
        ]);

        $content->update($validated);

        return redirect()->route('content.index')->with('success', 'Konten berhasil diperbarui.');
    }

    /**
     * Hapus konten
     */
    public function destroy(Content $content)
    {
        $content->delete();
        return back()->with('success', 'Konten berhasil dihapus.');
    }
    public function embed(Request $request, $id)
    {
        $selectedCity = $request->get('city');
        $selectedMonth = $request->get('month');

        // Ambil kategori + kontennya
        $category = ContentCategory::with([
            'contents' => function ($query) use ($selectedCity, $selectedMonth) {
                $query->with('city')->where('published', true)->orderBy('rank', 'asc');

                // Filter kota (jika ada)
                if ($selectedCity) {
                    $query->whereHas('city', function ($q) use ($selectedCity) {
                        $q->where('id', $selectedCity);
                    });
                }

                // Filter bulan (jika ada)
                if ($selectedMonth) {
                    $query->whereMonth('created_at', $selectedMonth)->whereYear('created_at', now()->year);
                }
            },
        ])->findOrFail($id);

        // Data dropdown
        $cities = City::select('id', 'name')->get();
        $months = collect(range(1, Carbon::now()->month))->map(
            fn($m) => [
                'value' => $m,
                'label' => Carbon::create()->month($m)->translatedFormat('F'),
            ],
        );

        // Render ke Inertia
        return inertia('Embed/Preview', [
            'category' => $category,
            'cities' => $cities,
            'months' => $months,
            'filters' => [
                'city' => $selectedCity,
                'month' => $selectedMonth,
            ],
        ]);
    }
}
