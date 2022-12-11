<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class UserInviteCode extends Model
{
    use HasFactory;

    protected $table = 'user_invite_codes';

    protected $fillable = [
        'invite_code', 'note', 'created_by', 'used_by', 'used_at',
    ];

    public static function createInvite($createdBy = null, $note = null)
    {
        return self::create([
            'invite_code' => Str::random(12),
            'note' => $note,
            'created_by' => $createdBy,
        ]);
    }

    public static function codeIsValid($inviteCode)
    {
        return self::where('invite_code', $inviteCode)
            ->whereNull('used_by')->exists();
    }

    public static function useInvite($usedBy, $inviteCode)
    {
        if (! self::codeIsValid($inviteCode)) {
            return false;
        }

        $invite = self::where('invite_code', $inviteCode)->first();
        $invite->used_by = $usedBy;
        $invite->used_at = now();

        return $invite->save();
    }

    public static function getInviteCodeStats()
    {
        $usedCodes = self::whereNotNull('used_by')->count();
        $activeCodes = self::whereNull('used_by')->count();

        return ['usedCodes' => $usedCodes, 'activeCodes' => $activeCodes];
    }
}
