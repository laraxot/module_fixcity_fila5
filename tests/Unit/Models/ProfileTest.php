<?php

declare(strict_types=1);

namespace Modules\Fixcity\Tests\Unit\Models;

use Illuminate\Database\QueryException;
use Modules\Fixcity\Models\Profile;
use Modules\User\Models\User;
use Tests\TestCase;

describe('Profile Model', function () {
    it('can be created with valid data', function () {
        $user = User::factory()->create();
        
        $profile = Profile::create([
            'user_id' => $user->id,
            'first_name' => 'Mario',
            'last_name' => 'Rossi',
            'phone' => '+39 123 456 7890',
            'address' => 'Via Roma 123, Milano',
        ]);

        expect($profile)
            ->toBeInstanceOf(Profile::class)
            ->user_id->toBe($user->id)
            ->first_name->toBe('Mario')
            ->last_name->toBe('Rossi')
            ->phone->toBe('+39 123 456 7890')
            ->address->toBe('Via Roma 123, Milano');
    });

    it('belongs to a user', function () {
        $user = User::factory()->create();
        $profile = Profile::factory()->create([
            'user_id' => $user->id,
        ]);

        expect($profile->user)
            ->toBeInstanceOf(User::class)
            ->id->toBe($user->id);
    });

    it('can store personal information', function () {
        $profile = Profile::factory()->create([
            'first_name' => 'Giulia',
            'last_name' => 'Bianchi',
            'phone' => '+39 987 654 3210',
            'address' => 'Via Garibaldi 456, Roma',
        ]);

        expect($profile->first_name)->toBe('Giulia');
        expect($profile->last_name)->toBe('Bianchi');
        expect($profile->phone)->toBe('+39 987 654 3210');
        expect($profile->address)->toBe('Via Garibaldi 456, Roma');
    });

    it('can generate full name', function () {
        $profile = Profile::factory()->create([
            'first_name' => 'Antonio',
            'last_name' => 'Verdi',
        ]);

        $fullName = $profile->first_name . ' ' . $profile->last_name;
        expect($fullName)->toBe('Antonio Verdi');
    });

    it('can store optional fields', function () {
        $profile = Profile::factory()->create([
            'first_name' => 'Maria',
            'last_name' => 'Neri',
            'phone' => null,
            'address' => null,
        ]);

        expect($profile->phone)->toBeNull();
        expect($profile->address)->toBeNull();
    });

    it('can be queried by user', function () {
        $user = User::factory()->create();
        $profile = Profile::factory()->create([
            'user_id' => $user->id,
        ]);

        $userProfile = Profile::where('user_id', $user->id)->first();
        
        expect($userProfile)
            ->toBeInstanceOf(Profile::class)
            ->id->toBe($profile->id);
    });

    it('can be searched by name', function () {
        $profile = Profile::factory()->create([
            'first_name' => 'Roberto',
            'last_name' => 'Gialli',
        ]);

        $searchResults = Profile::where('first_name', 'like', '%Roberto%')
            ->orWhere('last_name', 'like', '%Gialli%')
            ->get();
        
        expect($searchResults)->toContain($profile);
    });

    it('can be filtered by phone number', function () {
        $profile = Profile::factory()->create([
            'phone' => '+39 555 123 4567',
        ]);

        $phoneResults = Profile::where('phone', 'like', '%555%')->get();
        
        expect($phoneResults)->toContain($profile);
    });

    it('can be filtered by address', function () {
        $profile = Profile::factory()->create([
            'address' => 'Via Milano 789, Torino',
        ]);

        $addressResults = Profile::where('address', 'like', '%Torino%')->get();
        
        expect($addressResults)->toContain($profile);
    });

    it('maintains data integrity constraints', function () {
        // Test that required fields are enforced
        expect(function () {
            Profile::create([]);
        })->toThrow(QueryException::class);
    });

    it('can be soft deleted if implemented', function () {
        $profile = Profile::factory()->create();
        
        // Check if soft deletes are implemented
        if (method_exists($profile, 'trashed')) {
            $profile->delete();
            expect($profile->trashed())->toBeTrue();
            
            $trashedProfile = Profile::withTrashed()->find($profile->id);
            expect($trashedProfile)->not->toBeNull();
        } else {
            // If no soft deletes, test regular deletion
            $profileId = $profile->id;
            $profile->delete();
            
            expect(Profile::find($profileId))->toBeNull();
        }
    });

    it('can be updated', function () {
        $profile = Profile::factory()->create([
            'first_name' => 'Original',
            'last_name' => 'Name',
        ]);

        $profile->update([
            'first_name' => 'Updated',
            'last_name' => 'Name',
        ]);

        expect($profile->first_name)->toBe('Updated');
        expect($profile->last_name)->toBe('Name');
    });

    it('tracks creation and update times', function () {
        $profile = Profile::factory()->create();
        
        expect($profile->created_at)->not->toBeNull();
        expect($profile->updated_at)->not->toBeNull();
        
        // Update the profile
        $profile->update(['first_name' => 'Updated']);
        
        expect($profile->updated_at)->toBeGreaterThan($profile->created_at);
    });

    it('can handle special characters in names', function () {
        $profile = Profile::factory()->create([
            'first_name' => 'José',
            'last_name' => 'O\'Connor',
        ]);

        expect($profile->first_name)->toBe('José');
        expect($profile->last_name)->toBe('O\'Connor');
    });

    it('can handle international phone numbers', function () {
        $profile = Profile::factory()->create([
            'phone' => '+1 (555) 123-4567',
        ]);

        expect($profile->phone)->toBe('+1 (555) 123-4567');
    });

    it('can handle long addresses', function () {
        $longAddress = 'Via delle Rose 123, Piano Terra, Appartamento 4B, 20100 Milano, Italia';
        $profile = Profile::factory()->create([
            'address' => $longAddress,
        ]);

        expect($profile->address)->toBe($longAddress);
    });
});
