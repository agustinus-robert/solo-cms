<?php

namespace Modules\Portal\Notifications\ScheduleTeacher\Manage;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Modules\HRMS\Models\EmployeePosition;
use Modules\HRMS\Models\EmployeeScheduleSubmissionTeacher;

class ApprovedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $schedule;
    public $position;

    /**
     * Create a new notification instance.
     */
    public function __construct(EmployeeScheduleSubmissionTeacher $schedule, ?EmployeePosition $position)
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
            ->subject('Selamat, jadwal kerja disetujui')
            ->greeting('Selamat, jadwal kerja yang kamu ajukan disetujui')
            ->line('Pengajuan jadwal pekerjaan #' . $this->schedule->employee->user->name . ' telah disetujui, klik tombol di bawah untuk lihat detailnya.')
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
            'message' => 'Pengajuan jadwal kerja' . $this->schedule->employee->user->name . ' telah disetujui, cek sekarang!',
            'icon' => 'mdi mdi-calendar-multiselect',
            'color' => 'success',
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
