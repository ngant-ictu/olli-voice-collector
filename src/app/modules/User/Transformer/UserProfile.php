<?php
namespace User\Transformer;

use League\Fractal\TransformerAbstract;
use User\Model\UserProfile as UserProfileModel;

class UserProfile extends TransformerAbstract
{
    public function transform(UserProfileModel $userprofile)
    {
        return [
            'id' => (string) $userprofile->id,
            'uid' => (string) $userprofile->uid,
            'address' => (string) $userprofile->address,
            'gender' => (string) $userprofile->gender,
            'dob' => (string) $userprofile->dob,
            'city' => (string) $userprofile->city,
            'country' => (string) $userprofile->country,
            'bio' => (string) $userprofile->bio,
            'voiceregion' => (string) $userprofile->voiceregion,
            'voiceage' => (string) $userprofile->voiceage,
            'point' => (string) $userprofile->point
        ];
    }
}
