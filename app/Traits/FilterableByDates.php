<?php

namespace App\Traits;

use Carbon\Carbon;

trait FilterableByDates
{
    /**
     * Last minute scope
     * 
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $column
     * 
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeLastMinute($query, $column = 'created_at')
    {
        return $query->whereBetween($column, [Carbon::now()->subMinute(), Carbon::now()]);
    }

    /**
     * Last x minutes scope
     * 
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $column
     * 
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeLastMinutes($query, $minutes, $column = 'created_at')
    {
        return $query->whereBetween($column, [Carbon::now()->subMinutes($minutes), Carbon::now()]);
    }

    /**
     * Last hour scope
     * 
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $column
     * 
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeLastHour($query, $column = 'created_at')
    {
        return $query->whereBetween($column, [Carbon::now()->subHour(), Carbon::now()]);
    }

    /**
     * Last x hours scope
     * 
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $column
     * 
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeLastHours($query, $hours, $column = 'created_at')
    {
        return $query->whereBetween($column, [Carbon::now()->subHours($hours), Carbon::now()]);
    }

    /**
     * Past hour scope
     * 
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $column
     * 
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePastHour($query, $column = 'created_at')
    {
        return $query->whereBetween($column, [Carbon::now()->startOfHour()->subHour(), Carbon::now()->startOfHour()]);
    }

    /**
     * Last day scope
     * 
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $column
     * 
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeLastDay($query, $column = 'created_at')
    {
        return $query->whereBetween($column, [Carbon::now()->subDay(), Carbon::now()]);
    }

    /**
     * Last x days scope
     * 
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $column
     * 
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeLastDays($query, $days, $column = 'created_at')
    {
        return $query->whereBetween($column, [Carbon::now()->subDays($days), Carbon::now()]);
    }

    /**
     * Last Week scope
     * 
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $column
     * 
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeLastWeek($query, $column = 'created_at')
    {
        return $query->whereBetween($column, [Carbon::now()->subWeek(), Carbon::now()]);
    }

    /**
     * Last x Weeks scope
     * 
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $column
     * 
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeLastWeeks($query, $weeks, $column = 'created_at')
    {
        return $query->whereBetween($column, [Carbon::now()->subWeeks($weeks), Carbon::now()]);
    }

    /** 
     * Past week scope
     * 
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $column
     * 
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePastWeek($query, $column = 'created_at')
    {
        return $query->whereBetween($column, [Carbon::now()->startOfWeek()->subWeek(), Carbon::now()->startOfWeek()]);
    }

    /**
     * Last Month scope
     * 
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $column
     * 
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeLastMonth($query, $column = 'created_at')
    {
        return $query->whereBetween($column, [Carbon::now()->subMonth(), Carbon::now()]);
    }

    /**
     * Last x Months scope
     * 
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $column
     * 
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeLastMonths($query, $months, $column = 'created_at')
    {
        return $query->whereBetween($column, [Carbon::now()->subMonths($months), Carbon::now()]);
    }

    /**
     * Past Month scope
     * 
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $column
     * 
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePastMonth($query, $column = 'created_at')
    {
        return $query->whereBetween($column, [Carbon::now()->startOfMonth()->subMonth(), Carbon::now()->startOfMonth()]);
    }

    /**
     * Last Year scope
     * 
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $column
     * 
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeLastYear($query, $column = 'created_at')
    {
        return $query->whereBetween($column, [Carbon::now()->subYear(), Carbon::now()]);
    }

    /**
     * Last x Years scope
     * 
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $column
     * 
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeLastYears($query, $years, $column = 'created_at')
    {
        return $query->whereBetween($column, [Carbon::now()->subYears($years), Carbon::now()]);
    }

    /**
     * Past Year scope
     * 
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $column
     * 
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePastYear($query, $column = 'created_at')
    {
        return $query->whereBetween($column, [Carbon::now()->startOfYear()->subYear(), Carbon::now()->startOfYear()]);
    }

    /**
     * Today scope
     * 
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $column
     * 
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeToday($query, $column = 'created_at')
    {
        return $query->whereDate($column, Carbon::today());
    }

    /**
     * Yesterday scope
     * 
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $column
     * 
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeYesterday($query, $column = 'created_at')
    {
        return $query->whereDate($column, Carbon::yesterday());
    }

    /**
     * Tomorrow scope
     * 
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $column
     * 
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeTomorrow($query, $column = 'created_at')
    {
        return $query->whereDate($column, Carbon::tomorrow());
    }

    /**
     * This Week scope
     * 
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $column
     * 
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeThisWeek($query, $column = 'created_at')
    {
        return $query->whereBetween($column, [Carbon::now()->startOfWeek(), Carbon::now()]);
    }

    /**
     * This Month scope
     * 
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $column
     * 
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeThisMonth($query, $column = 'created_at')
    {
        return $query->whereBetween($column, [Carbon::now()->startOfMonth(), Carbon::now()]);
    }

    /**
     * This x Months scope
     * 
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $column
     * 
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeThisMonths($query, $months, $column = 'created_at')
    {
        return $query->whereBetween($column, [Carbon::now()->startOfMonth()->subMonths($months - 1), Carbon::now()]);
    }

    /**
     * This Year scope
     * 
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $column
     * 
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeThisYear($query, $column = 'created_at')
    {
        return $query->whereBetween($column, [Carbon::now()->startOfYear(), Carbon::now()]);
    }

    /**
     * This x Years scope
     * 
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $column
     * 
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeThisYears($query, $years, $column = 'created_at')
    {
        return $query->whereBetween($column, [Carbon::now()->startOfYear()->subYears($years - 1), Carbon::now()]);
    }

    /**
     * Scope to filter by date range
     * 
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $column
     * 
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeDateRange($query, $from, $to, $column = 'created_at')
    {
        return $query->whereBetween($column, [Carbon::parse($from), Carbon::parse($to)]);
    }

    /**
     * Scope to filter by date range
     * 
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $column
     * 
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeDateRangeFrom($query, $from, $column = 'created_at')
    {
        return $query->where($column, '>=', Carbon::parse($from));
    }

    /**
     * Scope to filter by date range
     * 
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $column
     * 
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeDateRangeTo($query, $to, $column = 'created_at')
    {
        return $query->where($column, '<=', Carbon::parse($to));
    }
}
