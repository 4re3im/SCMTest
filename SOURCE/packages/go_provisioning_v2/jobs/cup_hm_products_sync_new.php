<?php

defined('C5_EXECUTE') or die("Access Denied.");

Loader::library('HotMaths/collections_v2/product');

class CupHmProductsSyncNew extends Job
{
    const INSERT_STATEMENT = <<<SQL
            INSERT INTO `HMProducts`
            (`productId`, `teacherProductId`, `subscriberType`)
            VALUES (?, ?, ?)
SQL;

    protected $jQueueBatchSize = 500;

    public function getJobName()
    {
        return t("CUP HM Products Sync");
    }

    public function getJobDescription()
    {
        return t("Syncs HM product locally");
    }

    public function run()
    {
        $db = Loader::db();

        $hmProductApi = new HMProduct();
        $products = $hmProductApi->getAllProducts();

        $db->StartTrans();
        $db->Execute('TRUNCATE HMProducts;');
        try {
            foreach ($products as $product) {
                $db->Execute(
                    static::INSERT_STATEMENT,
                    [
                        $product->productId,
                        $product->teacherProductId,
                        $product->subscriberType
                    ]
                );
            }
            $db->CompleteTrans();
        } catch (Exception $e) {
            $db->RollbackTrans();
            return $e->getMessage();
        }

        shell_exec(CRON_PROVISIONING_HM_PENDING);
    }
}
