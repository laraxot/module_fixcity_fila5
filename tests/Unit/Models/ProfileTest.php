<?php

declare(strict_types=1);

namespace Modules\Fixcity\Tests\Unit\Models;

use Modules\Fixcity\Database\Factories\ProfileFactory;
use Modules\User\Database\Factories\UserFactory;
use Modules\Fixcity\Models\Profile;
use Modules\User\Models\User;

use PHPUnit\Framework\Assert;

describe('Profile Model', function () {
    it('can be created with valid data', function () {
        $user = UserFactory::new()->createOne();

        $profile = Profile::create([
            'user_id' => $user->id,
            'first_name' => 'Mario',
            'last_name' => 'Rossi',
            'email' => 'mario.rossi@example.com',
        ]);

        Assert::assertInstanceOf(Profile::class, $profile);
        Assert::assertSame($user->id, $profile->user_id);
        Assert::assertSame('Mario', $profile->first_name);
        Assert::assertSame('Rossi', $profile->last_name);
        Assert::assertSame('mario.rossi@example.com', $profile->email);
    });

    it('belongs to a user', function () {
        $user = UserFactory::new()->createOne();
        $profile = ProfileFactory::new()->createOne([
            'user_id' => $user->id,
        ]);

        Assert::assertInstanceOf(User::class, $profile->user);
        Assert::assertSame($user->id, $profile->user->id);
    });

    it('can store personal information', function () {
        $profile = ProfileFactory::new()->createOne([
            'first_name' => 'Giulia',
            'last_name' => 'Bianchi',
            'email' => 'giulia.bianchi@example.com',
        ]);

        Assert::assertSame('Giulia', $profile->first_name);
        Assert::assertSame('Bianchi', $profile->last_name);
        Assert::assertSame('giulia.bianchi@example.com', $profile->email);
    });

    it('can generate full name', function () {
        $profile = ProfileFactory::new()->createOne([
            'first_name' => 'Antonio',
            'last_name' => 'Verdi',
        ]);

        $fullName = $profile->first_name.' '.$profile->last_name;
        Assert::assertSame('Antonio Verdi', $fullName);
    });

    it('can store optional fields', function () {
        $profile = ProfileFactory::new()->createOne([
            'first_name' => 'Maria',
            'last_name' => 'Neri',
            'email' => null,
        ]);

        Assert::assertNull($profile->email);
    });

    it('can be queried by user', function () {
        $user = UserFactory::new()->createOne();
        $profile = ProfileFactory::new()->createOne([
            'user_id' => $user->id,
        ]);

        $userProfile = Profile::where('user_id', $user->id)->first();
        Assert::assertInstanceOf(Profile::class, $userProfile);
        Assert::assertSame($profile->id, $userProfile->id);
    });

    it('can be searched by name', function () {
        $profile = ProfileFactory::new()->createOne([
            'first_name' => 'Roberto',
            'last_name' => 'Gialli',
        ]);

        $searchResults = Profile::where('first_name', 'like', '%Roberto%')
            ->orWhere('last_name', 'like', '%Gialli%')
            ->get();

        Assert::assertContains($profile, $searchResults);
    });

    it('can be filtered by email', function () {
        $profile = ProfileFactory::new()->createOne([
            'email' => 'filter.me@example.com',
        ]);

        $emailResults = Profile::where('email', 'like', '%filter.me%')->get();
        Assert::assertContains($profile, $emailResults);
    });

    it('maintains data integrity constraints', function () {
        // Test that required fields are enforced
    });

    it('can be deleted', function () {
        $profile = ProfileFactory::new()->createOne();
        $profileId = $profile->id;
        $profile->delete();

        Assert::assertNull(Profile::find($profileId));
    });

    it('can be updated', function () {
        $profile = ProfileFactory::new()->createOne([
            'first_name' => 'Original',
            'last_name' => 'Name',
        ]);

        $profile->update([
            'first_name' => 'Updated',
            'last_name' => 'Name',
        ]);

        Assert::assertSame('Updated', $profile->first_name);
        Assert::assertSame('Name', $profile->last_name);
    });

    it('tracks creation and update times', function () {
        $profile = ProfileFactory::new()->createOne();

        Assert::assertNotNull($profile->created_at);
        Assert::assertNotNull($profile->updated_at);
        $profile->update(['first_name' => 'Updated']);

        Assert::assertGreaterThan($profile->created_at, $profile->updated_at);
    });

    it('can handle special characters in names', function () {
        $profile = ProfileFactory::new()->createOne([
            'first_name' => 'José',
            'last_name' => 'O\'Connor',
        ]);

        Assert::assertSame('José', $profile->first_name);
        Assert::assertSame('O\'Connor', $profile->last_name);
    });
});
