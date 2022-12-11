<?php

namespace App\Http\Controllers;

use Cache;
use Illuminate\Http\Request;

class FilterController extends Controller
{
    public function getTitle()
    {
        $filters = Cache::get('filters.title', []);

        return view('admin.filters.title', [
            'filters' => $filters,
        ]);
    }

    public function postTitle(Request $request)
    {
        $filters = Cache::get('filters.title', []);
        $filterValue = $request->input('filter_value');

        if ($filterValue) {
            if (! in_array($filterValue, $filters)) {
                $filters[] = $this->cleanFilterValue($filterValue);

                Cache::forever('filters.title', $filters);
            }
        }

        return view('admin.filters.title', [
            'filters' => Cache::get('filters.title'),
        ]);
    }

    public function deleteTitle(Request $request)
    {
        $filterValue = $request->input('filter_value');

        $filters = Cache::get('filters.title');
        $newFilters = [];
        if ($filterValue) {
            foreach ($filters as $filter) {
                $check = $this->cleanFilterValue($filterValue);

                if ($check !== $filter) {
                    $newFilters[] = $filter;
                }
            }
        }

        Cache::forever('filters.title', $newFilters);

        return view('admin.filters.title', [
            'filters' => Cache::get('filters.title'),
        ]);
    }

    private function cleanFilterValue($value)
    {
        return trim($value);
    }
}
