<?php

declare(strict_types=1);

namespace Modules\Fixcity\Tests\Unit\Models;

use Illuminate\Support\Collection;
use Modules\Comment\Database\Factories\CommentFactory;
use Modules\Fixcity\Database\Factories\TicketFactory;
use Modules\Fixcity\Models\Ticket;
use Modules\Comment\Models\Comment;
use Modules\Fixcity\Tests\TestCase;
use Modules\User\Database\Factories\UserFactory;
use Modules\User\Models\User;
use PHPUnit\Framework\Assert;

uses(TestCase::class);

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
            'commentable_id' => $ticket->id,
            'commentable_type' => $ticket->getMorphClass(),
            'commentator_id' => $user->id,
            'commentator_type' => $user->getMorphClass(),
            'original_text' => 'This is a test comment',
            'text' => 'This is a test comment',
        ]);

        Assert::assertInstanceOf(Comment::class, $comment);

        Assert::assertSame($ticket->id, $comment->commentable_id);

        Assert::assertSame($user->id, $comment->commentator_id);

        Assert::assertSame('This is a test comment', $comment->original_text);
    });

    it('belongs to a ticket', function () {
        $ticket = TicketFactory::new()->createOne();
        $comment = CommentFactory::new()->createOne([
            'commentable_id' => $ticket->id,
            'commentable_type' => $ticket->getMorphClass(),
        ]);

        Assert::assertInstanceOf(Ticket::class, $comment->commentable);

        Assert::assertSame($ticket->id, $comment->commentable->id);
    });

    it('belongs to a user', function () {
        $user = UserFactory::new()->createOne();
        $comment = CommentFactory::new()->createOne([
            'commentator_id' => $user->id,
            'commentator_type' => $user->getMorphClass(),
        ]);

        Assert::assertInstanceOf(User::class, $comment->commentator);

        Assert::assertSame($user->id, $comment->commentator->id);
    });

    it('can store rich content', function () {
        $comment = CommentFactory::new()->createOne([
            'original_text' => 'This is a **rich** comment with *formatting*',
        ]);

        Assert::assertSame('This is a **rich** comment with *formatting*', $comment->original_text);

        Assert::assertStringContainsString('**rich**', $comment->original_text);
        Assert::assertStringContainsString('*formatting*', $comment->original_text);
    });

    it('tracks creation and update times', function () {
        $comment = CommentFactory::new()->createOne();

        Assert::assertNotNull($comment->created_at);
        Assert::assertNotNull($comment->updated_at);
        // Update the comment
        $comment->update(['original_text' => 'Updated content']);

        Assert::assertGreaterThan($comment->created_at, $comment->updated_at);
    });

    it('can be queried by ticket', function () {
        $ticket = TicketFactory::new()->createOne();
        $comments = CommentFactory::new()->count(3)->create([
            'commentable_id' => $ticket->id,
            'commentable_type' => $ticket->getMorphClass(),
        ]);

        $ticketComments = Comment::where('commentable_id', $ticket->id)
            ->where('commentable_type', $ticket->getMorphClass())
            ->get();

        Assert::assertCount(3, $ticketComments);
        foreach ($ticketComments as $comment) {
            Assert::assertSame($ticket->id, $comment->commentable_id);
        }
    });

    it('can be queried by user', function () {
        $user = UserFactory::new()->createOne();
        $comments = CommentFactory::new()->count(3)->create([
            'commentator_id' => $user->id,
            'commentator_type' => $user->getMorphClass(),
        ]);

        $userComments = Comment::query()->where('commentator_id', $user->id)
            ->where('commentator_type', $user->getMorphClass())
            ->get();

        Assert::assertCount(3, $userComments);
        foreach ($userComments as $comment) {
            Assert::assertSame($user->id, $comment->commentator_id);
        }
    });

    it('can be filtered by ticket id', function () {
        $ticket = TicketFactory::new()->createOne();
        $comment = CommentFactory::new()->createOne([
            'commentable_id' => $ticket->id,
            'commentable_type' => $ticket->getMorphClass(),
        ]);

        $ticketComments = Comment::query()
            ->where('commentable_id', $ticket->id)
            ->where('commentable_type', $ticket->getMorphClass())
            ->get();

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
            'original_text' => 'Special search term in comment',
        ]);

        $searchResults = Comment::where('original_text', 'like', '%search term%')->get();

        Assert::assertContains($comment, $searchResults);
    });

    it('maintains data integrity constraints')->todo();

    it('can be deleted', function () {
        $comment = CommentFactory::new()->createOne();
        $commentId = $comment->id;

        $comment->delete();

        Assert::assertNull(Comment::find($commentId));
    });

    it('can be associated with attachments if implemented', function () {
        $comment = CommentFactory::new()->createOne();

        // Test if media library is implemented
        if (method_exists($comment, 'getMedia')) {
            Assert::assertInstanceOf(Collection::class, $comment->getMedia());
        }
    });
});
