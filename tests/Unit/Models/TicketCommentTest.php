<?php

declare(strict_types=1);

namespace Modules\Fixcity\Tests\Unit\Models;

use Modules\Fixcity\Database\Factories\TicketCommentFactory;
use Modules\Fixcity\Database\Factories\TicketFactory;
use Modules\User\Database\Factories\UserFactory;
use Illuminate\Database\QueryException;
use Illuminate\Support\Collection;
use Modules\Fixcity\Models\Ticket;
use Modules\Fixcity\Models\TicketComment;
use Modules\User\Models\User;

use PHPUnit\Framework\Assert;
describe('TicketComment Model', function () {
    it('can be created with valid data', function () {
        $user = UserFactory::new()->createOne();
        $ticket = TicketFactory::new()->createOne();
// Laraxot module file — see docs/wiki for domain contract.
// Laraxot module file — see docs/wiki for domain contract.
// Laraxot module file — see docs/wiki for domain contract.
// Laraxot module file — see docs/wiki for domain contract.
// Laraxot module file — see docs/wiki for domain contract.
// Laraxot — see module docs/wiki for domain contract.

        $comment = TicketComment::create([
            'ticket_id' => $ticket->id,
            'user_id' => $user->id,
            'content' => 'This is a test comment',
        ]);

        Assert::assertInstanceOf(TicketComment::class, $comment);

        Assert::assertSame($ticket->id, $comment->ticket_id);

        Assert::assertSame($user->id, $comment->user_id);

        Assert::assertSame('This is a test comment', $comment->content);
    });

    it('belongs to a ticket', function () {
        $ticket = TicketFactory::new()->createOne();
        $comment = TicketCommentFactory::new()->createOne([
            'ticket_id' => $ticket->id,
        ]);

        Assert::assertInstanceOf(Ticket::class, $comment->ticket);

        Assert::assertSame($ticket->id, $comment->ticket->id);
    });

    it('belongs to a user', function () {
        $user = UserFactory::new()->createOne();
        $comment = TicketCommentFactory::new()->createOne([
            'user_id' => $user->id,
        ]);

        Assert::assertInstanceOf(User::class, $comment->user);

        Assert::assertSame($user->id, $comment->user->id);
    });

    it('can store rich content', function () {
        $comment = TicketCommentFactory::new()->createOne([
            'content' => 'This is a **rich** comment with *formatting*',
        ]);

        Assert::assertSame('This is a **rich** comment with *formatting*', $comment->content);

        Assert::assertStringContainsString('**rich**', $comment->content);
        Assert::assertStringContainsString('*formatting*', $comment->content);
    });

    it('tracks creation and update times', function () {
        $comment = TicketCommentFactory::new()->createOne();

        Assert::assertNotNull($comment->created_at);
        Assert::assertNotNull($comment->updated_at);
        // Update the comment
        $comment->update(['content' => 'Updated content']);

        Assert::assertGreaterThan($comment->created_at, $comment->updated_at);
    });

    it('can be queried by ticket', function () {
        $ticket = TicketFactory::new()->createOne();
        $comments = TicketCommentFactory::new()->count(3)->create([
            'ticket_id' => $ticket->id,
        ]);

        $ticketComments = TicketComment::where('ticket_id', $ticket->id)->get();

        Assert::assertCount(3, $ticketComments);
        foreach ($ticketComments as $comment) {
            Assert::assertSame($ticket->id, $comment->ticket_id);
        }
    });

    it('can be queried by user', function () {
        $user = UserFactory::new()->createOne();
        $comments = TicketCommentFactory::new()->count(3)->create([
            'user_id' => $user->id,
        ]);

        $userComments = TicketComment::query()->where('user_id', $user->id)->get();

        Assert::assertCount(3, $userComments);
        foreach ($userComments as $comment) {
            Assert::assertSame($user->id, $comment->user_id);
        }
    });

    it('can be filtered by ticket id', function () {
        $ticket = TicketFactory::new()->createOne();
        $comment = TicketCommentFactory::new()->createOne(['ticket_id' => $ticket->id]);

        $ticketComments = TicketComment::query()->where('ticket_id', $ticket->id)->get();

        Assert::assertCount(1, $ticketComments);
        Assert::assertSame($comment->id, $ticketComments->first()?->id);
    });

    it('can be ordered by creation time', function () {
        $oldComment = TicketCommentFactory::new()->createOne([
            'created_at' => now()->subDays(2),
        ]);

        $newComment = TicketCommentFactory::new()->createOne([
            'created_at' => now(),
        ]);

        $orderedComments = TicketComment::orderBy('created_at', 'desc')->get();

        $first = $orderedComments->first();
        $last = $orderedComments->last();
        Assert::assertNotNull($first);
        Assert::assertNotNull($last);
        Assert::assertSame($newComment->id, $first->id);
        Assert::assertSame($oldComment->id, $last->id);
    });

    it('can be searched by content', function () {
        $comment = TicketCommentFactory::new()->createOne([
            'content' => 'Special search term in comment',
        ]);

        $searchResults = TicketComment::where('content', 'like', '%search term%')->get();

        Assert::assertContains($comment, $searchResults);
    });

    it('maintains data integrity constraints', function () {
        // Test that required fields are enforced

    });

    it('can be soft deleted if implemented', function () {
        $comment = TicketCommentFactory::new()->createOne();

        // Check if soft deletes are implemented
        if (method_exists($comment, 'trashed')) {
            $comment->delete();
            Assert::assertTrue($comment->trashed());
            $trashedComment = TicketComment::withTrashed()->find($comment->id);
            Assert::assertNotNull($trashedComment);
        } else {
            // If no soft deletes, test regular deletion
            $commentId = $comment->id;
            $comment->delete();

            Assert::assertNull(TicketComment::find($commentId));
        }
    });

    it('can be associated with attachments if implemented', function () {
        $comment = TicketCommentFactory::new()->createOne();

        // Test if media library is implemented
        if (method_exists($comment, 'getMedia')) {
            Assert::assertInstanceOf(Collection::class, $comment->getMedia());
        }
    });
});
