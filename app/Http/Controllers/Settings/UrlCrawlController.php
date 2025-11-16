<?php
namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\ContentCategory;
use Illuminate\Http\Request;
use App\Models\UrlCrawl;
use Inertia\Inertia;

class UrlCrawlController extends Controller
{
    public function index()
    {
        $urlCrawls = UrlCrawl::with(['city', 'category'])
            ->orderBy('created_at', 'desc')
            ->get();

        $contentCategory = ContentCategory::all();
        $city = City::where('status', 'active')->get();

        return Inertia::render('Settings/UrlCrawls/Index', [
            'urlCrawls' => $urlCrawls,
            'contentCategory' => $contentCategory,
            'city' => $city,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required',
            'city' => 'required',
            'tag' => 'required|string',
        ]);

        UrlCrawl::create([
            'name' => $request->name,
            'category' => $request->category,
            'city' => $request->city,
            'tag' => $request->tag,
            'actor_id' => 'apify~instagram-hashtag-scraper',
        ]);

        return back()->with('success', 'URL Crawl berhasil ditambahkan!');
    }

    public function update(Request $request, UrlCrawl $url_crawl)
    {
        $url_crawl->update($request->only(['name', 'category', 'city', 'tag']));
        return back()->with('success', 'URL Crawl berhasil diperbarui!');
    }

    public function destroy(UrlCrawl $url_crawl)
    {
        $url_crawl->delete();
        return back()->with('success', 'Data berhasil dihapus!');
    }
}
