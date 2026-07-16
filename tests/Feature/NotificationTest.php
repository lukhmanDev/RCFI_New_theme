<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Notification;
use App\Models\NotificationRecipient;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NotificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_creating_notification_creates_recipient_records_for_all_users(): void
    {
        // 1. Create 3 users
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $user3 = User::factory()->create();

        // 2. Create a notification
        $notification = Notification::create([
            'title' => 'New Project Uploaded',
            'message' => 'Project Center A has been approved.',
            'url' => '/projects/1',
        ]);

        // 3. Verify recipient records exist for all users
        $this->assertEquals(3, NotificationRecipient::count());
        $this->assertTrue(NotificationRecipient::where('user_id', $user1->id)->where('notification_id', $notification->id)->exists());
        $this->assertTrue(NotificationRecipient::where('user_id', $user2->id)->where('notification_id', $notification->id)->exists());
        $this->assertTrue(NotificationRecipient::where('user_id', $user3->id)->where('notification_id', $notification->id)->exists());
    }

    public function test_user_can_mark_notification_as_read_individually(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $notification = Notification::create([
            'title' => 'Test Notification',
            'message' => 'This is a test notification message.',
            'url' => '#',
        ]);

        $recipient1 = NotificationRecipient::where('user_id', $user1->id)->first();
        $recipient2 = NotificationRecipient::where('user_id', $user2->id)->first();

        // Verify initially unread
        $this->assertFalse($recipient1->is_read);
        $this->assertFalse($recipient2->is_read);

        // Mark recipient 1 as read via POST endpoint
        $response = $this->actingAs($user1)->post("/admin/notifications/{$recipient1->id}/mark-read");
        $response->assertOk();
        $response->assertJsonPath('success', true);

        // Verify recipient 1 is read but recipient 2 remains unread
        $this->assertTrue($recipient1->fresh()->is_read);
        $this->assertNotNull($recipient1->fresh()->read_at);
        $this->assertFalse($recipient2->fresh()->is_read);
    }

    public function test_user_can_mark_all_notifications_as_read(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        Notification::create(['title' => 'Notification 1', 'message' => 'Message 1']);
        Notification::create(['title' => 'Notification 2', 'message' => 'Message 2']);

        $userRecipients = NotificationRecipient::where('user_id', $user->id)->get();
        $otherRecipients = NotificationRecipient::where('user_id', $otherUser->id)->get();

        $this->assertCount(2, $userRecipients);
        $this->assertCount(2, $otherRecipients);
        
        foreach ($userRecipients as $r) {
            $this->assertFalse($r->is_read);
        }

        // Mark all read for $user
        $response = $this->actingAs($user)->post('/admin/notifications/mark-all-read');
        $response->assertOk();
        $response->assertJsonPath('success', true);

        // Verify $user's notifications are all read
        foreach (NotificationRecipient::where('user_id', $user->id)->get() as $r) {
            $this->assertTrue($r->is_read);
            $this->assertNotNull($r->read_at);
        }

        // Verify other user's notifications remain unread
        foreach (NotificationRecipient::where('user_id', $otherUser->id)->get() as $r) {
            $this->assertFalse($r->is_read);
        }
    }
}
