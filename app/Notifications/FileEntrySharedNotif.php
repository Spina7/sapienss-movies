<?php

namespace App\Notifications;

use App\FileEntry;
use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Action;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Collection;

class FileEntrySharedNotif extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * @var FileEntry[]|Collection
     */
    public $fileEntries;

    /**
     * @var User
     */
    public $sharer;

    /**
     * @param array[] $entryIds
     * @param User $sharer
     */
    public function __construct($entryIds, User $sharer)
    {
        $this->sharer = $sharer;
        $this->fileEntries = FileEntry::whereIn('id', $entryIds)->get();
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database', 'mail'];
    }

    /**
     * @param  mixed  $notifiable
     * @return MailMessage
     */
    public function toMail($notifiable)
    {
        $message = (new MailMessage)
            ->subject(__('Files shared on :siteName', ['siteName' => config('app.name')]))
            ->line($this->getFirstLine());

            foreach ($this->getFileLines() as $line) {
                $message->line('- ' . $line['content']);
            }

            $message->action(__('View now'), url('drive/shares'));

            return $message;
    }

    /**
     * @param User $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        $data = [
            'image' => 'people',
            'mainAction' => [
                'action' => '',
            ],
            'lines' => [
                [
                    'content' => $this->getFirstLine(),
                ],
            ],
        ];

        $data['lines'] = array_merge($data['lines'], $this->getFileLines());

        return $data;
    }

    /**
     * @return array
     */
    private function getFileLines()
    {
        $lines = [];

        foreach ($this->fileEntries as $fileEntry) {
            $lines[] = [
                'icon' => kebab_case($fileEntry->type),
                'content' => $fileEntry->name,
                'action' => ['action' => '/drive/shares'],
            ];
        }

        return $lines;
    }

    /**
     * @return string
     */
    private function getFirstLine()
    {
        $fileCount = $this->fileEntries->count();
        $username = $this->sharer->display_name;

        if ($this->fileEntries->count() === 1) {
            return __(':username shared a file with you:', ['username' => $username]);
        } else {
            return __(':username has shared :count files with you:', ['username' => $username, 'count' => $fileCount]);
        }
    }
}
