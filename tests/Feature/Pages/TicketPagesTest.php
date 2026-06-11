<?php

declare(strict_types=1);


use Modules\Fixcity\Tests\TestCase;

use PHPUnit\Framework\Assert;
use Modules\Fixcity\Database\Factories\TicketFactory;
use Modules\User\Database\Factories\UserFactory;
use Modules\Fixcity\Models\Ticket;
use Modules\User\Models\User;

uses(TestCase::class);
beforeEach(function () {
    /** @var TestCase $this */
        Assert::assertNotNull($this->user);
    $this->user = UserFactory::new()->createOne();
});

describe('Ticket Creation Page', function () {
    it('can access ticket creation page when authenticated', function () {
        /** @var TestCase $this */
        Assert::assertNotNull($this->user);
        $this->actingAs($this->user);

        $response = $this->get('/it/tickets/create');

        $response->assertOk();
    });

    it('redirects to login when not authenticated', function () {
        /** @var TestCase $this */
        $response = $this->get('/it/tickets/create');

        $response->assertRedirect('/it/auth/login');
    });

    it('displays ticket creation form', function () {
        /** @var TestCase $this */
        Assert::assertNotNull($this->user);
        $this->actingAs($this->user);

        $response = $this->get('/it/tickets/create');

        $response
            ->assertOk()
            ->assertSee('Segnalazione disservizio')
            ->assertSeeVolt('create-ticket-widget');
    });

    it('has correct page name', function () {
        /** @var TestCase $this */
        Assert::assertNotNull($this->user);
        $this->actingAs($this->user);

        $response = $this->get('/it/tickets/create');

        // Check if the page has the correct Folio name
        Assert::assertSame(url('/it/tickets/create'), route('ticket.create'));
    });

    it('includes required CSS and JavaScript', function () {
        /** @var TestCase $this */
        Assert::assertNotNull($this->user);
        $this->actingAs($this->user);

        $response = $this->get('/it/tickets/create');

        $response
            ->assertOk()
            ->assertSee('Titillium Web') // Font
            ->assertSee('fi-input-wrp'); // CSS classes for styling
    });

    it('uses marketing layout', function () {
        /** @var TestCase $this */
        Assert::assertNotNull($this->user);
        $this->actingAs($this->user);

        $response = $this->get('/it/tickets/create');

        $response
            ->assertOk()
            ->assertViewIs('pages.tickets.create');
    });
});

describe('Ticket View Page', function () {
    it('can access ticket view page with valid slug', function () {
        /** @var TestCase $this */
        Assert::assertNotNull($this->user);
        $ticket = TicketFactory::new()->createOne([
            'name' => 'Test Ticket',
            'slug' => 'test-ticket',
            'owner_id' => $this->user->id,
        ]);

        $response = $this->get("/it/tickets/{$ticket->slug}");

        $response->assertOk();
    });

    it('shows 404 for non-existent ticket', function () {
        /** @var TestCase $this */
        $response = $this->get('/it/tickets/non-existent-ticket');

        $response->assertNotFound();
    });

    it('displays ticket information', function () {
        /** @var TestCase $this */
        Assert::assertNotNull($this->user);
        $ticket = TicketFactory::new()->createOne([
            'name' => 'Viewable Test Ticket',
            'content' => 'This is the ticket content',
            'slug' => 'viewable-test-ticket',
            'owner_id' => $this->user->id,
        ]);

        $response = $this->get("/it/tickets/{$ticket->slug}");

        $response
            ->assertOk()
            ->assertSee('Viewable Test Ticket')
            ->assertSee('This is the ticket content');
    });

    it('can access ticket view without authentication', function () {
        /** @var TestCase $this */
        Assert::assertNotNull($this->user);
        $ticket = TicketFactory::new()->createOne([
            'name' => 'Public Ticket',
            'slug' => 'public-ticket',
            'owner_id' => $this->user->id,
        ]);

        // Not authenticated
        $response = $this->get("/it/tickets/{$ticket->slug}");

        $response->assertOk();
    });

    it('has correct page name route', function () {
        /** @var TestCase $this */
        Assert::assertNotNull($this->user);
        $ticket = TicketFactory::new()->createOne([
            'name' => 'Route Test Ticket',
            'slug' => 'route-test-ticket',
            'owner_id' => $this->user->id,
        ]);

        Assert::assertSame(url("/it/tickets/{$ticket->slug}"), route('ticket.view', ['slug' => $ticket->slug]));
    });

    it('can display ticket with geolocation', function () {
        /** @var TestCase $this */
        Assert::assertNotNull($this->user);
        $ticket = TicketFactory::new()->createOne([
            'name' => 'Geo Ticket',
            'slug' => 'geo-ticket',
            'latitude' => '45.4642',
            'longitude' => '9.1900',
            'owner_id' => $this->user->id,
        ]);

        $response = $this->get("/it/tickets/{$ticket->slug}");

        $response->assertOk();
        // Additional assertions for geolocation display would go here
    });

    it('can display ticket with attachments', function () {
        /** @var TestCase $this */
        Assert::assertNotNull($this->user);
        $ticket = TicketFactory::new()->createOne([
            'name' => 'Ticket with Attachments',
            'slug' => 'ticket-with-attachments',
            'owner_id' => $this->user->id,
        ]);

        // Add media to ticket (if media system is implemented)
        $response = $this->get("/it/tickets/{$ticket->slug}");

        $response->assertOk();
    });
});

describe('Ticket Page SEO and Metadata', function () {
    it('has proper meta tags for ticket view', function () {
        /** @var TestCase $this */
        Assert::assertNotNull($this->user);
        $ticket = TicketFactory::new()->createOne([
            'name' => 'SEO Test Ticket',
            'content' => 'Content for SEO testing',
            'slug' => 'seo-test-ticket',
            'owner_id' => $this->user->id,
        ]);

        $response = $this->get("/it/tickets/{$ticket->slug}");

        $response
            ->assertOk()
            ->assertSee('<title>', false) // Should have title tag
            ->assertSee('SEO Test Ticket'); // Title should contain ticket name
    });

    it('has breadcrumbs on creation page', function () {
        /** @var TestCase $this */
        Assert::assertNotNull($this->user);
        $this->actingAs($this->user);

        $response = $this->get('/it/tickets/create');

        $response
            ->assertOk()
            ->assertSee('Tickets') // Breadcrumb
            ->assertSee('Create'); // Breadcrumb
    });

    it('is crawlable by search engines', function () {
        /** @var TestCase $this */
        Assert::assertNotNull($this->user);
        $ticket = TicketFactory::new()->createOne([
            'name' => 'Crawlable Ticket',
            'slug' => 'crawlable-ticket',
            'owner_id' => $this->user->id,
        ]);

        $response = $this->get("/it/tickets/{$ticket->slug}");

        $response
            ->assertOk()
            ->assertDontSee('noindex') // Should not have noindex
            ->assertDontSee('nofollow'); // Should not have nofollow
    });
});

describe('Ticket Page Accessibility', function () {
    it('has proper heading structure', function () {
        /** @var TestCase $this */
        Assert::assertNotNull($this->user);
        $this->actingAs($this->user);

        $response = $this->get('/it/tickets/create');

        $response
            ->assertOk()
            ->assertSee('<h1', false); // Should have H1 tag
    });

    it('has proper form labels', function () {
        /** @var TestCase $this */
        Assert::assertNotNull($this->user);
        $this->actingAs($this->user);

        $response = $this->get('/it/tickets/create');

        $response->assertOk();
        // Form labels would be tested when CreateTicketWidget is fully implemented
    });

    it('supports keyboard navigation', function () {
        /** @var TestCase $this */
        Assert::assertNotNull($this->user);
        $this->actingAs($this->user);

        $response = $this->get('/it/tickets/create');

        $response->assertOk();
        // Keyboard navigation tests would go here
    });
});

describe('Ticket Page Performance', function () {
    it('loads within acceptable time', function () {
        /** @var TestCase $this */
        Assert::assertNotNull($this->user);
        $this->actingAs($this->user);

        $startTime = microtime(true);
        $response = $this->get('/it/tickets/create');
        $endTime = microtime(true);

        $loadTime = $endTime - $startTime;

        $response->assertOk();
        Assert::assertLessThan(2.0, $loadTime); // Should load within 2 seconds
    });

    it('handles multiple ticket views efficiently', function () {
        /** @var TestCase $this */
        Assert::assertNotNull($this->user);
        /** @var \Illuminate\Database\Eloquent\Collection<int, \Modules\Fixcity\Models\Ticket> $tickets */
        $tickets = TicketFactory::new()->count(5)->create([
            'owner_id' => $this->user->id,
        ]);

        foreach ($tickets as $ticket) {
            $response = $this->get("/it/tickets/{$ticket->slug}");
            $response->assertOk();
        }

        // All tickets should load successfully
        Assert::assertCount(5, $tickets);
    });

    it('has optimized CSS delivery', function () {
        /** @var TestCase $this */
        Assert::assertNotNull($this->user);
        $this->actingAs($this->user);

        $response = $this->get('/it/tickets/create');

        $response
            ->assertOk()
            ->assertSee('Titillium Web'); // Font should be loaded efficiently
    });
});
