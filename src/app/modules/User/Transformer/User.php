<?php
namespace User\Transformer;

use League\Fractal\TransformerAbstract;
use User\Model\User as UserModel;
use User\Transformer\UserProfile as UserProfileTransformer;
use Moment\Moment;

class User extends TransformerAbstract
{
    protected $availableIncludes = [];
    protected $defaultIncludes = [
        'profile'
    ];

    public function transform(UserModel $user)
    {
        $humandatecreated = new Moment($user->datecreated);

        return [
            'id' => (string) $user->id,
            'fullname' => (string) $user->fullname,
            'screenname' => (string) $user->screenname,
            'email' => (string) $user->email,
            'groupid' => (string) $user->groupid,
            'status' =>  [
                'label' => (string) $user->getStatusName(),
                'value' => (string) $user->status,
                'style' => (string) $user->getStatusStyle()
            ],
            'verify' => [
                'label' => (string) $user->getVerifyName(),
                'value' => (string) $user->isverified,
                'style' => (string) $user->getVerifyStyle()
            ],
            'verifytype' => [
                'label' => (string) $user->getVerifyTypeName(),
                'value' => (string) $user->verifytype
            ],
            'avatar' => (string) $user->getAvatarJson(),
            'mobilenumber' => (string) $user->mobilenumber,
            'datecreated' => (string) $user->datecreated,
            'humandatecreated' => (string) $humandatecreated->format('d M Y, H:i')
        ];
    }

    public function includeProfile(UserModel $user)
    {
        $profile = $user->getProfile();

        return $this->item($profile, new UserProfileTransformer);
    }
}
