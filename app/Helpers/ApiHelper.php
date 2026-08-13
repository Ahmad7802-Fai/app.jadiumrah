<?php

namespace App\Helpers;

class ApiHelper
{
    public static function limit()
    {
        $limit = request()->limit ?? 20;
        $limit = max(1, min((int) $limit, 100));
        return $limit;
    }

    public static function page()
    {
        $page = request()->page ?? 1;
        return max(1, (int) $page);
    }

    public static function search($query, $fields = [])
    {
        $keyword = request()->search;
        if (!$keyword || empty($fields)) return $query;

        return $query->where(function ($q) use ($fields, $keyword) {
            foreach ($fields as $field) {
                $q->orWhere($field, 'LIKE', "%{$keyword}%");
            }
        });
    }
}
