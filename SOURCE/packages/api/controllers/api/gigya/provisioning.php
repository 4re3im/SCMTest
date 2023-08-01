<?php

class APIGigyaProvisioningController extends Controller
{
    private $pModel;

    public function __construct()
    {
        Loader::model('provisioning', 'go_provisioning');
        $this->pModel = new ProvisioningModel();
    }

    public function on_start()
    {
        parent::on_start();

        $view = View::getInstance();
        $view->setTheme(PageTheme::getByHandle('json_theme', $this->pkgHandle));
    }

    public function completeProvisioning()
    {
        Loader::library('AWS/S3/S3Service');

        $objectKey = $this->post('s3ObjectKey');
        $objectKeys = explode('_', $objectKey);
        $fileId = array_pop($objectKeys);

        $s3Service = new S3Service();
        $s3Service->useGigyaDataFlowConnection();
        $data = $s3Service->getObject(GIGYA_S3_BUCKET, $objectKey);

        if (!$data) {
            echo json_encode([
                'success' => false,
                'message' => 'Object not found.'
            ]);
            exit;
        }

        $json = json_decode($data->get('Body')->getContents());

        foreach ($json as $row) {
            $uId = str_replace('go', '', strtolower($row->UID));
            $errorCode = $row->_errorDetails->errorCode;
            $errorMessage = $row->_errorDetails->errorMessage;
            $this->updateProvisioningData(
                $fileId,
                $uId,
                "GigyaError",
                "$errorCode: $errorMessage"
            );
        }

        $this->markProvisioningComplete($fileId);

        echo json_encode(['success' => true]);
        exit;
    }

    private function updateProvisioningData($fileId, $uId, $status, $remarks)
    {
        $db = Loader::db();
        $sql = <<<SQL
          UPDATE ProvisioningUsers
          SET Status = ?, Remarks = ?
          WHERE FileID = ? AND uID = ?
SQL;
        return $db->Execute($sql, [$status, $remarks, $fileId, $uId]);
    }

    private function markProvisioningComplete($fileId)
    {
        $db = Loader::db();
        $sql = 'UPDATE ProvisioningFiles SET IsProvisionedInGigya = 1 WHERE ID = ?';

        return $db->Execute($sql, [$fileId]);
    }

    public function provisionInGigya()
    {
        $c = Page::getByPath('/dashboard/provisioning/setup');
        Loader::controller($c);
        $cnt = new DashboardProvisioningSetupController();
        $cnt->provisionInGigya();
        die;
    }
}
