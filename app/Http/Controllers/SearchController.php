<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'query' => 'nullable|string|max:255',
            'style' => 'nullable|string|max:255',
            'technique' => 'nullable|string|max:255',
            'theme' => 'nullable|string|max:255',
            'max-width' => 'nullable|integer|min:1',
            'max-height' => 'nullable|integer|min:1',
        ]);

        $query = $request->input('query');
        $style = $request->input('style');
        $technique = $request->input('technique');
        $theme = $request->input('theme');
        $maxWidth = $request->input('max-width');
        $maxHeight = $request->input('max-height');

        $queryBuilder = Item::where('state', 'Auction');
        $query = str_replace("'", "''", $query);
        if ($query) {
            $queryBuilder->where(function ($q) use ($query) {
                $q->whereRaw("tsvectors @@ plainto_tsquery('english', ?)", [$query]);
            });
        }
        else {
            
            $results = Item::all();
        }

        $queryBuilder->when($style, function ($q) use ($style) {
            return $q->where('style', $style);
        })
        ->when($technique, function ($q) use ($technique) {
            return $q->where('technique', $technique);
        })
        ->when($theme, function ($q) use ($theme) {
            return $q->where('theme', $theme);
        })
        ->when($maxWidth, function ($q) use ($maxWidth) {
            return $q->where('width', '<=', $maxWidth);
        })
        ->when($maxHeight, function ($q) use ($maxHeight) {
            return $q->where('height', '<=', $maxHeight);
        });


        $styles = Item::distinct()->pluck('style');
        $techniques = Item::distinct()->pluck('technique');
        $themes = Item::distinct()->pluck('theme');
        $results = $queryBuilder->paginate(12); 

        return view('pages.results', [
            'results' => $results,
            'query' => $query,
            'style' => $style,
            'technique' => $technique,
            'theme' => $theme,
            'maxWidth' => $maxWidth,
            'maxHeight' => $maxHeight,
            'styles' => $styles,      
            'techniques' => $techniques,
            'themes' => $themes       
        ]);
    }
}
