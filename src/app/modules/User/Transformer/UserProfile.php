<?php
namespace User\Transformer;

use League\Fractal\TransformerAbstract;
use User\Model\UserProfile as UserProfileModel;
use Moment\Moment;

class UserProfile extends TransformerAbstract
{
    public function transform(UserProfileModel $userprofile)
    {
        if ($userprofile->dob > 0) {
            $dob = new \DateTime();
            $dob->setTimestamp($userprofile->dob);
            $humandob = $dob->format('d/m/Y');
        } else {
            $humandob = '';
        }

        return [
            'id' => (int) $userprofile->id,
            'uid' => (int) $userprofile->uid,
            'address' => (string) $userprofile->address,
            'gender' =>  [
                'label' => (string) $userprofile->getGenderName(),
                'value' => (int) $userprofile->gender,
                'style' => (string) $userprofile->getGenderStyle(),
                'icon' => (string) $userprofile->getGenderIcon()
            ],
            'dob' => (int) $userprofile->dob,
            'humandob' => (string) $humandob,
            'city' => (string) $userprofile->city,
            'country' => (string) $userprofile->country,
            'bio' => (string) $userprofile->bio,
            'voiceregion' => (int) $userprofile->voiceregion,
            'point' => (int) $userprofile->point
        ];
    }
}
