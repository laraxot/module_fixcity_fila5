<?php

declare(strict_types=1);

namespace Modules\Fixcity\Tests\Unit\Models;

use Illuminate\Database\QueryException;
use Illuminate\Support\Collection;
use Modules\Fixcity\Models\Ticket;
use Modules\Fixcity\Models\TicketComment;
use Modules\User\Models\User;
use Tests\TestCase;

describe('TicketComment Model', function () {
    it('can be created with valid data', function () {
        $user = User::factory()->create();
        $ticket = Ticket::factory()->create();
        
        $comment = TicketComment::create([
            'ticket_id' => $ticket->id,
            'user_id' => $user->id,
            'content' => 'This is a test comment',
        ]);

        expect($comment)
            ->toBeInstanceOf(TicketComment::class)
            ->ticket_id->toBe($ticket->id)
            ->user_id->toBe($user->id)
            ->content->toBe('This is a test comment');
    });

    it('belongs to a ticket', function () {
        $ticket = Ticket::factory()->create();
        $comment = TicketComment::factory()->create([
            'ticket_id' => $ticket->id,
        ]);

        expect($comment->ticket)
            ->toBeInstanceOf(Ticket::class)
            ->id->toBe($ticket->id);
    });

    it('belongs to a user', function () {
        $user = User::factory()->create();
        $comment = TicketComment::factory()->create([
            'user_id' => $user->id,
        ]);

        expect($comment->user)
            ->toBeInstanceOf(User::class)
            ->id->toBe($user->id);
    });

    it('can store rich content', function () {
        $comment = TicketComment::factory()->create([
            'content' => 'This is a **rich** comment with *formatting*',
        ]);

        expect($comment->content)
            ->toBe('This is a **rich** comment with *formatting*')
            ->toContain('**rich**')
            ->toContain('*formatting*');
    });

    it('can be marked as internal', function () {
        $comment = TicketComment::factory()->create([
            'is_internal' => true,
        ]);

        expect($comment->is_internal)->toBeTrue();
    });

    it('can be marked as private', function () {
        $comment = TicketComment::factory()->create([
            'is_private' => true,
        ]);

        expect($comment->is_private)->toBeTrue();
    });

    it('tracks creation and update times', function () {
        $comment = TicketComment::factory()->create();
        
        expect($comment->created_at)->not->toBeNull();
        expect($comment->updated_at)->not->toBeNull();
        
        // Update the comment
        $comment->update(['content' => 'Updated content']);
        
        expect($comment->updated_at)->toBeGreaterThan($comment->created_at);
    });

    it('can be queried by ticket', function () {
        $ticket = Ticket::factory()->create();
        $comments = TicketComment::factory()->count(3)->create([
            'ticket_id' => $ticket->id,
        ]);

        $ticketComments = TicketComment::where('ticket_id', $ticket->id)->get();
        
        expect($ticketComments)->toHaveCount(3);
        foreach ($ticketComments as $comment) {
            expect($comment->ticket_id)->toBe($ticket->id);
        }
    });

    it('can be queried by user', function () {
        $user = User::factory()->create();
        $comments = TicketComment::factory()->count(3)->create([
            'user_id' => $user->id,
        ]);

        $userComments = TicketComment::factory()->where('user_id', $user->id)->get();
        
        expect($userComments)->toHaveCount(3);
        foreach ($userComments as $comment) {
            expect($comment->user_id)->toBe($user->id);
        }
    });

    it('can be filtered by visibility', function () {
        $publicComment = TicketComment::factory()->create([
            'is_internal' => false,
            'is_private' => false,
        ]);
        
        $internalComment = TicketComment::factory()->create([
            'is_internal' => true,
            'is_private' => false,
        ]);
        
        $privateComment = TicketComment::factory()->create([
            'is_internal' => false,
            'is_private' => true,
        ]);

        // Test public comments
        $publicComments = TicketComment::where('is_internal', false)
            ->where('is_private', false)
            ->get();
        expect($publicComments)->toContain($publicComment);
        expect($publicComments)->not->toContain($internalComment);
        expect($publicComments)->not->toContain($privateComment);

        // Test internal comments
        $internalComments = TicketComment::where('is_internal', true)->get();
        expect($internalComments)->toContain($internalComment);
        expect($internalComments)->not->toContain($publicComment);
        expect($internalComments)->not->toContain($privateComment);
    });

    it('can be ordered by creation time', function () {
        $oldComment = TicketComment::factory()->create([
            'created_at' => now()->subDays(2),
        ]);
        
        $newComment = TicketComment::factory()->create([
            'created_at' => now(),
        ]);

        $orderedComments = TicketComment::orderBy('created_at', 'desc')->get();
        
        expect($orderedComments->first()->id)->toBe($newComment->id);
        expect($orderedComments->last()->id)->toBe($oldComment->id);
    });

    it('can be searched by content', function () {
        $comment = TicketComment::factory()->create([
            'content' => 'Special search term in comment',
        ]);

        $searchResults = TicketComment::where('content', 'like', '%search term%')->get();
        
        expect($searchResults)->toContain($comment);
    });

    it('maintains data integrity constraints', function () {
        // Test that required fields are enforced
        expect(function () {
            TicketComment::create([]);
        })->toThrow(QueryException::class);
    });

    it('can be soft deleted if implemented', function () {
        $comment = TicketComment::factory()->create();
        
        // Check if soft deletes are implemented
        if (method_exists($comment, 'trashed')) {
            $comment->delete();
            expect($comment->trashed())->toBeTrue();
            
            $trashedComment = TicketComment::withTrashed()->find($comment->id);
            expect($trashedComment)->not->toBeNull();
        } else {
            // If no soft deletes, test regular deletion
            $commentId = $comment->id;
            $comment->delete();
            
            expect(TicketComment::find($commentId))->toBeNull();
        }
    });

    it('can be associated with attachments if implemented', function () {
        $comment = TicketComment::factory()->create();
        
        // Test if media library is implemented
        if (method_exists($comment, 'getMedia')) {
            expect($comment->getMedia())->toBeInstanceOf(Collection::class);
        }
    });
});
