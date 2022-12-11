<?php

namespace App\Mail;

use App\Media;
use App\MediaRemoteReference;
use App\User;
use App\UserMedia;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class JoinDateAnniversary extends Mailable
{
    use Queueable, SerializesModels;

    public $collectionSize;

    public $collectionReach;

    public $network;

    public $displayName;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(User $user)
    {
        $this->displayName = $user->display_name;

        $this->network = new \stdClass();
        $this->network->size = Media::count();
        $this->network->related = MediaRemoteReference::count();

        $this->collectionSize = UserMedia::where('user_id', $user->id)->count();
        $this->collectionReach = UserMedia::where('user_id', '!=', $user->id)
          ->whereIn('media_id', UserMedia::pluckMediaIds($user->id))
          ->count();
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('1 Year Anniversary!')
            ->bcc('moorlagt@gmail.com')
            ->markdown('emails.marketing.anniversary');
    }
}
