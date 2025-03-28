<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TaskAssigned extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public $task) {}

    /**
     * Define the delivery channels for this notification.
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Build the email message for the assigned task.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('You have a new task assigned')
            ->greeting("Hello $notifiable->name")
            ->line('A new task has been assigned to you.')
            ->line('Task: ' . $this->task->title)
            ->line('Description: ' . $this->task->description)
            ->line('Due Date: ' . $this->task->due_date)
            ->action('View Task', url('/tasks/' . $this->task->id))
            ->line('Thank you for using our application!');
    }

    /**
     * Provide an optional array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        return [
            // Optional: Add array representation if needed
        ];
    }
}
