<?php
namespace Record\Controller\V1;

use Shirou\Constants\ErrorCode;
use Shirou\UserException;
use Core\Controller\AbstractController;
use Core\Helper\Utils as Helper;
use Record\Model\Voice as VoiceModel;
use Record\Model\VoiceScript as VoiceScriptModel;
use Record\Transformer\VoiceScript as VoiceScriptTransformer;
use Record\Transformer\Voice as VoiceTransformer;

/**
 * @RoutePrefix("/v1/records")
 */
class IndexController extends AbstractController
{
    protected $recordPerPage = 30;

    /**
     * @Route("/scripts", methods={"GET"})
     */
    public function scriptsAction()
    {
        $sql = 'SELECT * FROM fly_voice_script AS r1 JOIN ';
        $sql .= '(SELECT CEIL(RAND() * (SELECT MAX(vs_id) FROM fly_voice_script)) AS id) AS r2 ';
        $sql .= 'WHERE r1.vs_id >= r2.id AND r1.vs_status = 1 ORDER BY r1.vs_id ASC LIMIT 1';
        $raw = $this->getDI()->get('db')->fetchOne($sql);

        if (!empty($raw)) {
            $myVoiceScript = new VoiceScriptModel();
            $myVoiceScript->id = (int) $raw['vs_id'];
            $myVoiceScript->command = (string) $raw['vs_command'];
            $myVoiceScript->text = (string) $raw['vs_text'];
            $myVoiceScript->status = (int) $raw['vs_status'];
            $myVoiceScript->datecreated = (int) $raw['vs_date_created'];

            return $this->createCollection(
                [$myVoiceScript],
                new VoiceScriptTransformer,
                'data'
            );
        } else {
            return $this->respondWithArray([], 'data');
        }
    }

    /**
     * @Route("/", methods={"POST"})
     */
    public function createAction()
    {
        $formData = (array) $this->request->getPost();
        $uid = (int) $this->getDI()->getAuth()->getUser()->id;

        $myVoice = VoiceModel::findFirst([
            'sid = :sid: AND uid = :uid:',
            'bind' => [
                'vsid' => (int) $formData['sid'],
                'uid' => (int) $uid
            ]
        ]);

        if ($myVoice) {
            throw new UserException(ErrorCode::DATA_DUPLICATE);
        }

        $myVoice = new VoiceModel();
        $myVoice->assign([
            'vsid' => (int) $formData['sid'],
            'uid' => (int) $uid,
            'status' => (int) VoiceModel::STATUS_PENDING
        ]);

        if (!$myVoice->create()) {
            throw new UserException(ErrorCode::DATA_CREATE_FAIL);
        }

        // Update recording times in Firebase

        return $this->createItem(
            $myVoice,
            new VoiceTransformer,
            'data'
        );
    }
}
