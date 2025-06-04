<?php

namespace App\Support\Helpers;

use App\Contracts\Enums\UserStates;
use App\Models\User;
use Illuminate\Http\Request;

class MatchQueryBuilder
{
    private const LIMIT = 100;

    private object $baseQuery;

    /**
     * Create a new class instance.
     */
    public function __construct(private readonly Request $request)
    {
        $this->baseQuery = User::query()
            ->where('status', UserStates::ACTIVE)
            ->whereNot('id', $request->user()->id);
    }

    public function countryFilter(): self
    {

        if ($this->request->query('country')) {
            $this->baseQuery = $this->baseQuery->where('country_id', $this->request->query('country'));
        } else {
            $this->baseQuery = $this->baseQuery->where('country_id', $this->request->user()->country_id);
        }

        return $this;
    }

    public function nameFilter(): self
    {
        $name = $this->request->query('name');

        if ($name) {
            $this->baseQuery = $this->baseQuery->where('firstname', 'like', "%$name%");
        }

        return $this;
    }

    public function ageFilter(): self
    {
        $maxAge = $this->request->query('age_max');
        $minAge = $this->request->query('age_min');

        if ($maxAge && $minAge) {
            $this->baseQuery = $this->baseQuery->whereBetween('age', [$minAge, $maxAge]);
        }

        return $this;
    }

    public function activeFilter(): self
    {
        if ($this->request->query('active')) {
            $this->baseQuery = $this->baseQuery->where('active', 1);
        }

        return $this;
    }

    public function genderFilter(): self
    {
        if (! $this->request->query('gender')) {
            $this->baseQuery = $this->baseQuery->whereNot('gender', $this->request->user()->gender);
        } elseif ($this->request->query('gender') && $this->request->query('gender') !== 'both') {
            $this->baseQuery = $this->baseQuery->where('gender', $this->request->query('gender'));
        }

        return $this;
    }

    public function excludeReactedUsers(): self
    {
        $userId = $this->request->user()->id;
        
        $this->baseQuery = $this->baseQuery->whereNotExists(function ($query) use ($userId) {
            $query->select('id')
                ->from('reactions')
                ->whereColumn('recipient', 'users.id')
                ->where('actor', $userId)
                ->whereIn('type', ['like', 'block']);
        });

        return $this;
    }

    public function get()
    {
        return $this->baseQuery
            ->with(['images', 'profile', 'country'])
            ->excludeReactedUsers()
            ->inRandomOrder()
            ->limit(self::LIMIT)
            ->get();
    }
}
