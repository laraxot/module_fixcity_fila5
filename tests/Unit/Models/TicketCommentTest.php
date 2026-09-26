<?php

declare(strict_types=1);

namespace Modules\Fixcity\Tests\Unit\Models;

use Illuminate\Support\Collection;
use Modules\Comment\Database\Factories\CommentFactory;
use Modules\Fixcity\Database\Factories\TicketFactory;
use Modules\Fixcity\Models\Ticket;
use Modules\Comment\Models\Comment;
use Modules\User\Database\Factories\UserFactory;
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

        $comment = Comment::create([
<<<<<<< HEAD
            'commentable_id' => $ticket->id,
            'commentable_type' => $ticket->getMorphClass(),
            'commentator_id' => $user->id,
            'commentator_type' => $user->getMorphClass(),
            'original_text' => 'This is a test comment',
            'text' => 'This is a test comment',
=======
            'ticket_id' => $ticket->id,
            'user_id' => $user->id,
            'content' => 'This is a test comment',
>>>>>>> laraxot/dev
        ]);

        Assert::assertInstanceOf(Comment::class, $comment);

<<<<<<< HEAD
        Assert::assertSame($ticket->id, $comment->commentable_id);

        Assert::assertSame($user->id, $comment->commentator_id);

        Assert::assertSame('This is a test comment', $comment->original_text);
=======
        Assert::assertSame($ticket->id, $comment->ticket_id);

        Assert::assertSame($user->id, $comment->user_id);

        Assert::assertSame('This is a test comment', $comment->content);
>>>>>>> laraxot/dev
    });

    it('belongs to a ticket', function () {
        $ticket = TicketFactory::new()->createOne();
        $comment = CommentFactory::new()->createOne([
<<<<<<< HEAD
            'commentable_id' => $ticket->id,
            'commentable_type' => $ticket->getMorphClass(),
        ]);

        Assert::assertInstanceOf(Ticket::class, $comment->commentable);

        Assert::assertSame($ticket->id, $comment->commentable->id);
=======
            'ticket_id' => $ticket->id,
        ]);

        Assert::assertInstanceOf(Ticket::class, $comment->ticket);

        Assert::assertSame($ticket->id, $comment->ticket->id);
>>>>>>> laraxot/dev
    });

    it('belongs to a user', function () {
        $user = UserFactory::new()->createOne();
        $comment = CommentFactory::new()->createOne([
<<<<<<< HEAD
            'commentator_id' => $user->id,
            'commentator_type' => $user->getMorphClass(),
        ]);

        Assert::assertInstanceOf(User::class, $comment->commentator);

        Assert::assertSame($user->id, $comment->commentator->id);
=======
            'user_id' => $user->id,
        ]);

        Assert::assertInstanceOf(User::class, $comment->user);

        Assert::assertSame($user->id, $comment->user->id);
>>>>>>> laraxot/dev
    });

    it('can store rich content', function () {
        $comment = CommentFactory::new()->createOne([
<<<<<<< HEAD
            'original_text' => 'This is a **rich** comment with *formatting*',
        ]);

        Assert::assertSame('This is a **rich** comment with *formatting*', $comment->original_text);

        Assert::assertStringContainsString('**rich**', $comment->original_text);
        Assert::assertStringContainsString('*formatting*', $comment->original_text);
=======
            'content' => 'This is a **rich** comment with *formatting*',
        ]);

        Assert::assertSame('This is a **rich** comment with *formatting*', $comment->content);

        Assert::assertStringContainsString('**rich**', $comment->content);
        Assert::assertStringContainsString('*formatting*', $comment->content);
>>>>>>> laraxot/dev
    });

    it('tracks creation and update times', function () {
        $comment = CommentFactory::new()->createOne();

        Assert::assertNotNull($comment->created_at);
        Assert::assertNotNull($comment->updated_at);
        // Update the comment
<<<<<<< HEAD
        $comment->update(['original_text' => 'Updated content']);
=======
        $comment->update(['content' => 'Updated content']);
>>>>>>> laraxot/dev

        Assert::assertGreaterThan($comment->created_at, $comment->updated_at);
    });

    it('can be queried by ticket', function () {
        $ticket = TicketFactory::new()->createOne();
        $comments = CommentFactory::new()->count(3)->create([
<<<<<<< HEAD
            'commentable_id' => $ticket->id,
            'commentable_type' => $ticket->getMorphClass(),
        ]);

        $ticketComments = Comment::where('commentable_id', $ticket->id)
            ->where('commentable_type', $ticket->getMorphClass())
            ->get();

        Assert::assertCount(3, $ticketComments);
        foreach ($ticketComments as $comment) {
            Assert::assertSame($ticket->id, $comment->commentable_id);
=======
            'ticket_id' => $ticket->id,
        ]);

        $ticketComments = Comment::where('ticket_id', $ticket->id)->get();

        Assert::assertCount(3, $ticketComments);
        foreach ($ticketComments as $comment) {
            Assert::assertSame($ticket->id, $comment->ticket_id);
>>>>>>> laraxot/dev
        }
    });

    it('can be queried by user', function () {
        $user = UserFactory::new()->createOne();
        $comments = CommentFactory::new()->count(3)->create([
<<<<<<< HEAD
            'commentator_id' => $user->id,
            'commentator_type' => $user->getMorphClass(),
        ]);

        $userComments = Comment::query()->where('commentator_id', $user->id)
            ->where('commentator_type', $user->getMorphClass())
            ->get();

        Assert::assertCount(3, $userComments);
        foreach ($userComments as $comment) {
            Assert::assertSame($user->id, $comment->commentator_id);
=======
            'user_id' => $user->id,
        ]);

        $userComments = Comment::query()->where('user_id', $user->id)->get();

        Assert::assertCount(3, $userComments);
        foreach ($userComments as $comment) {
            Assert::assertSame($user->id, $comment->user_id);
>>>>>>> laraxot/dev
        }
    });

    it('can be filtered by ticket id', function () {
        $ticket = TicketFactory::new()->createOne();
<<<<<<< HEAD
        $comment = CommentFactory::new()->createOne([
            'commentable_id' => $ticket->id,
            'commentable_type' => $ticket->getMorphClass(),
        ]);

        $ticketComments = Comment::query()
            ->where('commentable_id', $ticket->id)
            ->where('commentable_type', $ticket->getMorphClass())
            ->get();
=======
        $comment = CommentFactory::new()->createOne(['ticket_id' => $ticket->id]);

        $ticketComments = Comment::query()->where('ticket_id', $ticket->id)->get();
>>>>>>> laraxot/dev

        Assert::assertCount(1, $ticketComments);
        Assert::assertSame($comment->id, $ticketComments->first()?->id);
    });

    it('can be ordered by creation time', function () {
        $oldComment = CommentFactory::new()->createOne([
            'created_at' => now()->subDays(2),
        ]);

        $newComment = CommentFactory::new()->createOne([
            'created_at' => now(),
        ]);

        $orderedComments = Comment::orderBy('created_at', 'desc')->get();

        $first = $orderedComments->first();
        $last = $orderedComments->last();
        Assert::assertNotNull($first);
        Assert::assertNotNull($last);
        Assert::assertSame($newComment->id, $first->id);
        Assert::assertSame($oldComment->id, $last->id);
    });

    it('can be searched by content', function () {
        $comment = CommentFactory::new()->createOne([
<<<<<<< HEAD
            'original_text' => 'Special search term in comment',
        ]);

        $searchResults = Comment::where('original_text', 'like', '%search term%')->get();
=======
            'content' => 'Special search term in comment',
        ]);

        $searchResults = Comment::where('content', 'like', '%search term%')->get();
>>>>>>> laraxot/dev

        Assert::assertContains($comment, $searchResults);
    });

    it('maintains data integrity constraints')->todo();

<<<<<<< HEAD
    it('can be deleted', function () {
        $comment = CommentFactory::new()->createOne();
        $commentId = $comment->id;

        $comment->delete();

        Assert::assertNull(Comment::find($commentId));
=======
    it('can be soft deleted if implemented', function () {
        $comment = CommentFactory::new()->createOne();

        // Check if soft deletes are implemented
        if (method_exists($comment, 'trashed')) {
            $comment->delete();
            Assert::assertTrue($comment->trashed());
            $trashedComment = Comment::withTrashed()->find($comment->id);
            Assert::assertNotNull($trashedComment);
        } else {
            // If no soft deletes, test regular deletion
            $commentId = $comment->id;
            $comment->delete();

            Assert::assertNull(Comment::find($commentId));
        }
>>>>>>> laraxot/dev
    });

    it('can be associated with attachments if implemented', function () {
        $comment = CommentFactory::new()->createOne();

        // Test if media library is implemented
        if (method_exists($comment, 'getMedia')) {
            Assert::assertInstanceOf(Collection::class, $comment->getMedia());
        }
    });
});
