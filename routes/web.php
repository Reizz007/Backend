<?php

use App\Models\Setting;
use Illuminate\Support\Facades\Route;

// Serve sitemap.xml directly with XML headers for Googlebot
Route::get('/sitemap.xml', function () {
    $path = public_path('sitemap.xml');
    if (file_exists($path)) {
        return response()->file($path, [
            'Content-Type' => 'application/xml; charset=utf-8',
        ]);
    }

    return response('<?xml version="1.0" encoding="UTF-8"?><urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"><url><loc>https://dyzzstore.store/</loc></url></urlset>', 200, [
        'Content-Type' => 'application/xml; charset=utf-8',
    ]);
});

// Serve robots.txt directly
Route::get('/robots.txt', function () {
    $path = public_path('robots.txt');
    if (file_exists($path)) {
        return response()->file($path, [
            'Content-Type' => 'text/plain; charset=utf-8',
        ]);
    }

    return response("User-agent: *\nAllow: /\nSitemap: https://dyzzstore.store/sitemap.xml\n", 200, [
        'Content-Type' => 'text/plain; charset=utf-8',
    ]);
});

// Serve React SPA Frontend if built into public/
Route::get('/{any}', function () {
    $indexPath = public_path('index.html');
    if (! file_exists($indexPath)) {
        return view('welcome');
    }

    $html = file_get_contents($indexPath);

    // Dynamic SEO & Search Keyword Injection from Admin Database Settings
    try {
        $seoTitle = Setting::get('seo_title', 'Dyzzstore | Premier AI Tools Marketplace & Game Top-Up');
        $seoDesc = Setting::get('seo_description', 'Discover, buy, and top-up MLBB diamonds, Magic Chess, Free Fire, and premier AI software tools. Instant delivery via Cambodia National KHQR or Card.');
        $seoKeywords = Setting::get('seo_keywords', 'Dyzzstore, MLBB topup, Diamond Mobile Legends, Magic Chess, Free Fire topup, AI tools, Midjourney, ChatGPT, perpetual license, KHQR Bakong payment, cheap game topup Cambodia');
        $googleVerification = Setting::get('google_verification', 'RhwAQDXFLfwzFSBq0YM1kouGUUJ228ZjagAQ3Zr6K_I');
        $seoRobots = Setting::get('seo_robots', 'index, follow');

        if ($seoTitle) {
            $escapedTitle = e($seoTitle);
            $html = preg_replace('/<title>.*?<\/title>/i', "<title>{$escapedTitle}</title>", $html);
            $html = preg_replace('/<meta property="og:title" content=".*?"/i', '<meta property="og:title" content="'.$escapedTitle.'"', $html);
            $html = preg_replace('/<meta name="twitter:title" content=".*?"/i', '<meta name="twitter:title" content="'.$escapedTitle.'"', $html);
        }

        if ($seoDesc) {
            $escapedDesc = e($seoDesc);
            $html = preg_replace('/<meta name="description" content=".*?"/i', '<meta name="description" content="'.$escapedDesc.'"', $html);
            $html = preg_replace('/<meta property="og:description" content=".*?"/i', '<meta property="og:description" content="'.$escapedDesc.'"', $html);
            $html = preg_replace('/<meta name="twitter:description" content=".*?"/i', '<meta name="twitter:description" content="'.$escapedDesc.'"', $html);
        }

        if ($seoKeywords) {
            $escapedKw = e($seoKeywords);
            $html = preg_replace('/<meta name="keywords" content=".*?"/i', '<meta name="keywords" content="'.$escapedKw.'"', $html);
        }

        if ($googleVerification) {
            $escapedGv = e($googleVerification);
            $html = preg_replace('/<meta name="google-site-verification" content=".*?"/i', '<meta name="google-site-verification" content="'.$escapedGv.'"', $html);
        }

        if ($seoRobots) {
            $escapedRobots = e($seoRobots);
            $html = preg_replace('/<meta name="robots" content=".*?"/i', '<meta name="robots" content="'.$escapedRobots.'"', $html);
        }
    } catch (Throwable) {
        // Fallback to static HTML if database is temporarily unavailable
    }

    return response($html, 200)
        ->header('Content-Type', 'text/html; charset=utf-8');
})->where('any', '.*');
