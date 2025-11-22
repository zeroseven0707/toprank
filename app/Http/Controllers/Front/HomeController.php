<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use App\Models\BlogCategory;
use App\Models\City;
use App\Models\Content;
use App\Models\ContentCategory;
use App\Models\CrawlData;
use App\Models\Section;
use App\Models\UrlCrawl;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $selectedCategory = $request->get('category');
        $selectedCity = $request->get('city') ?? City::where('is_current', true)->value('id');
        $selectedMonth = $request->get('month') ?? Carbon::now()->month;

        $sections = Section::with([
            'contentCategory.contents' => function ($query) use ($selectedCity, $selectedMonth) {
                $query->with('city')->where('published', true)->orderBy('rank', 'asc');

                if ($selectedCity) {
                    $query->whereHas('city', function ($q) use ($selectedCity) {
                        $q->where('id', $selectedCity);
                    });
                }

                if ($selectedMonth) {
                    $query->whereMonth('created_at', $selectedMonth)->whereYear('created_at', now()->year);
                }
            },
        ])
            ->where('status', 'active')
            ->when($selectedCategory, function ($query) use ($selectedCategory) {
                $query->whereHas('contentCategory', function ($q) use ($selectedCategory) {
                    $q->where('id', $selectedCategory);
                });
            })
            ->orderBy('order', 'asc')
            ->get();

        $categories = ContentCategory::select('id', 'name')->get();
        $cities = City::select('id', 'name')->get();

        $months = collect(range(1, Carbon::now()->month))->map(
            fn($m) => [
                'value' => $m,
                'label' => Carbon::create()->month($m)->translatedFormat('F'),
            ],
        );

        return Inertia::render('Welcome', [
            'sections' => $sections,
            'categories' => $categories,
            'cities' => $cities,
            'months' => $months,
            'filters' => [
                'category' => $selectedCategory,
                'city' => $selectedCity,
                'month' => $selectedMonth,
            ],
        ]);
    }

    public function dashboard()
    {
        return Inertia::render('Dashboard', [
            'stats' => [
                'users' => User::count(),
                'blogs' => Blog::count(),
                'contents' => Content::count(),
                'contentCategories' => ContentCategory::count(),
                'cities' => City::count(),
                'urlCrawls' => UrlCrawl::count(),
            ],
            'recentBlogs' => Blog::latest()
                ->take(5)
                ->get(['id', 'title', 'created_at']),
            'dataUrlCrawl' => CrawlData::with('urlCrawl:id,name')
                ->latest()
                ->get(['id', 'ownerFullName', 'url', 'scraped_at', 'url_crawl_id'])
                ->take(5),
        ]);
    }
}
