<?php

namespace Modules\Portal\Notifications\Schedule\Submission;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Modules\HRMS\Models\EmployeeScheduleSubmission;
use Modules\HRMS\Models\EmployeePosition;

class SubmissionNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $schedule;
    public $position;

    /**
     * Create a new notification instance.
     */
    public function __construct(EmployeeScheduleSubmission $schedule, ?EmployeePosition $position)
    {
        $this->schedule = $schedule;
        $this->position = $position;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Seseorang mengajukan jadwal kerja')
            ->greeting('Seseorang mengajukan jadwal kerja')
            ->line($this->schedule->creator->user->name . ' mengajukan jadwal pekerjaan #' . $this->schedule->employee->user->name . ', klik tombol di bawah untuk lihat detailnya.')
            ->action('Periksa sekarang', route('portal::schedule.manages.show', ['schedule' => $this->schedule->id]))
            ->line('Jika Anda membutuhkan informasi lebih lanjut, segera hubungi kami untuk menindak lanjuti.')
            ->line('Terima kasih telah menggunakan layanan kami.');
    }

    /**
     * Get the array representation of the notification.
     */
    public function toDatabase($notifiable)
    {
        return [
            'message' => $this->schedule->creator->user->name . ' mengajukan jadwal kerja' . $this->schedule->employee->user->name . ', cek sekarang!',
            'icon' => 'mdi mdi-calendar-multiselect',
            'color' => 'info',
            'link' => route('portal::schedule.manages.show', ['schedule' => $this->schedule->id])
        ];
    }

    /**
     * Determine the notification's delivery delay.
     */
    public function withDelay($notifiable)
    {
        return [
            'mail' => now()->addSeconds(5),
            'database' => now(),
        ];
    }
}
