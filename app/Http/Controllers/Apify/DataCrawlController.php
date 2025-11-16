<?php

namespace App\Http\Controllers\Apify;

use App\Http\Controllers\Controller;
use App\Models\Content;
use App\Models\ContentCategory;
use App\Models\CrawlData;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class DataCrawlController extends Controller
{
    public function index(Request $request)
    {
        $period = $request->query('period', now()->format('Y-m'));

        $data = CrawlData::with('urlCrawl')->whereYear('scraped_at', substr($period, 0, 4))->whereMonth('scraped_at', substr($period, 5, 2))->latest()->paginate(10)->withQueryString();

        $categories = ContentCategory::select('id', 'name')->get();

        return Inertia::render('Settings/UrlCrawls/CrawlsData', [
            'crawls' => $data,
            'categories' => $categories,
            'period' => $period,
        ]);
    }

    public function show($id)
    {
        $data = CrawlData::findOrFail($id);
        return Inertia::render('Settings/UrlCrawls/CrawlsDataShow', [
            'crawl' => $data,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category_content_id' => 'required|exists:content_categories,id',
            'maps_link' => 'required|string',
            'image' => 'nullable|string',
            'description' => 'nullable|string',
            'rank' => [
                'integer',
                Rule::unique('contents')->where(function ($query) use ($request) {
                    return $query->where('category_content_id', $request->category_content_id);
                }),
            ],
        ]);

        Content::create($validated);

        return back()->with('success', 'Konten berhasil disimpan.');
    }
}
