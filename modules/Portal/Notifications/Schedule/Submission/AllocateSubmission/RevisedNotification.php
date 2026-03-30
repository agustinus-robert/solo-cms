<?php

namespace Modules\Portal\Notifications\Schedule\Submission\AllocateSubmission;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Modules\HRMS\Models\EmployeePosition;
use Modules\HRMS\Models\EmployeeScheduleSubmission;

class RevisedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $schedule;
    public $position;

    /**
     * Create a new notification instance.
     */
    public function __construct(EmployeeScheduleSubmission $schedule)
    {
        $this->schedule = $schedule;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via($notifiable)
    {
        return ['database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Maaf jadwal kerja anda perlu direvisi')
            ->greeting('Jadwal kerja perlu direvisi')
            ->line('Jadwal kerja atas nama ' . $this->schedule->employee->user->name . ' perlu dilakukan revisi, klik tombol di bawah untuk lihat detailnya.')
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
            'message' => $this->schedule->employee->user->name . ' telah merevisi jadwal kerja yang anda ajukan, cek sekarang!',
            'icon' => 'mdi mdi-calendar-multiselect',
            'color' => 'warning',
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
