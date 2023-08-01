<?php

Loader::library('gigya/GSSDK');
Loader::library('gigya/BaseGigya');
Loader::library('gigya/GigyaExport');

class GigyaDataFlow extends BaseGigya
{
    const METHOD_CREATE_DATA_FLOW = 'idx.createDataflow';
    const METHOD_SET_DATA_FLOW = 'idx.setDataflow';

    public static $FILE = 'file';
    public static $S3 = 's3';
    public static $CUSTOM = 'custom';
    // GCAP-541 Campion added by machua/mtanada 20191003
    public static $LITE = 'lite';
    public static $RESET_PASSWORD = 'resetPassword';
    public static $BULK_DELETE = 'bulkDelete';

    public $id;
    public $name;
    public $description;
    public $steps;
    public $lastRunTime;
    public $currentFile;

    public function save()
    {
        $data = json_encode([
            'name' => $this->name,
            'description' => $this->description,
            'steps' => $this->steps,
            'lastRunTime' => $this->lastRunTime
        ]);

        $this->setAPIKey(GIGYA_MIGRATION_API_KEY);
        $request = $this->newRequest(static::METHOD_CREATE_DATA_FLOW);
        $request->setParam('data', $data);

        $response = $request->send();

        if ($response->getInt('errorCode') > 0) {
            return false;
        }

        return $this->id = $response->getString('id');
    }

    public function update($dataFlowId)
    {
        $data = json_encode([
            'id' => $dataFlowId,
            'name' => $this->name,
            'description' => $this->description,
            'steps' => $this->steps
        ]);

        // Set to ssomaster API key
        $this->setAPIKey(GIGYA_MIGRATION_API_KEY);

        $request = $this->newRequest(static::METHOD_SET_DATA_FLOW);
        $request->setParam('data', $data);

        $response = $request->send();

        if ($response->getInt('errorCode') > 0) {
            return false;
        }

        return $response->getInt('errorCode');
    }

    // GCAP-541 Campion added by machua/mtanada 20191004
    public function getSteps($type)
    {
        switch ($type) {
            case self::$S3:
                $exporter = new GigyaExport();
                $stepsJson = $exporter->exportFromS3(GIGYA_MIGRATION_STEPS_PATH);
                return $this->setPrivateFields($stepsJson);
                break;
            case self::$FILE:
                break;
            case self::$CUSTOM:
                return $this->getDefaultSteps();
                break;
            case self::$LITE:
                return $this->getLiteSteps();
                break;
            case self::$RESET_PASSWORD:
                return $this->getResetPasswordSteps();
                break;
            case self::$BULK_DELETE:
                return $this->getBulkDeleteSteps();
                break;
            default :
                break;
        }
    }

    public function getDefaultSteps()
    {
        $gigyaS3Key = GIGYA_S3_KEY;
        $gigyaS3SecretKey = GIGYA_S3_SECRET;
        $gigyaS3ResultPath = GIGYA_S3_RESULT_PATH;
        $gigyaS3BucketName = GIGYA_S3_BUCKET;
        $gigyaUploadFullPath = GIGYA_S3_UPLOAD_PATH;

        // GCAP-530 Modified by Shane Camus 10/15/19
        $steps = <<<STEPS
            [
                {
                    "id": "Read files from S3",
                    "type": "datasource.read.amazon.s3",
                    "params": {
                        "bucketName": "$gigyaS3BucketName",
                        "accessKey": "$gigyaS3Key",
                        "secretKey": "$gigyaS3SecretKey",
                        "objectKeyPrefix": "$gigyaUploadFullPath"
                    },
                    "next": [
                        "Parse json file"
                    ]
                },
                {
                    "id": "Parse json file",
                    "type": "file.parse.json",
                    "params": {
                        "addFilename": false
                    },
                    "next": [
                        "Inject jobId"
                    ]
                },
                {
                    "id": "Inject jobId",
                    "type": "field.add",
                    "params": {
                        "fields": [
                            {
                                "field": "data.idxImportJobID",
                                "value": "\${jobId}"
                            }
                        ]
                    },
                    "next": [
                        "Is account existing?"
                    ]
                },
                {
                    "id": "Is account existing?",
                    "type": "datasource.lookup.gigya.account",
                    "params": {
                        "select": "UID, profile.email, loginIDs.emails",
                        "handleFieldConflicts": "take_lookup",
                        "mismatchBehavior": "error",
                        "lookupFields": [
                            {
                                "sourceField": "profile.email",
                                "gigyaField": "loginIDs.emails"
                            },
                            {
                                "sourceField": "profile.email",
                                "gigyaField": "profile.email"
                            }
                        ],
                        "lookupFieldsOperator": "OR",
                        "matchBehavior": "process",
                        "isCaseSensitive": false,
                        "from": "accounts",
                        "batchSize": 200,
                        "maxConcurrency": 1
                    },
                    "next": [
                        "Remove fields from existing",
                        "Create matched report"
                    ],
                    "error": [
                        "Create unmatched report",
                        "Import account"
                    ]
                },
                {
                    "id": "Remove fields from existing",
                    "type": "field.remove",
                    "params": {
                    "fields": [
                        "password",
                        "profile"
                    ]
                    },
                    "next": [
                        "Import account"
                    ]
                },
                {
                    "id": "Import account",
                    "type": "datasource.write.gigya.importaccount",
                    "params": {
                        "importPolicy": "upsert",
                        "handleIdentityConflicts": "connect",
                        "maxConnections": 20,
                        "addResponse": false
                    },
                    "next": [
                        "Record loaded data"
                    ],
                    "error": [
                        "Create error report"
                    ]
                },
                {
                    "id": "Create error report",
                    "type": "file.format.json",
                    "params": {
                        "fileName": "error_logs_\${now}_\${jobId}.json",
                        "maxFileSize": 5000,
                        "createEmptyFile": false
                    },
                    "next": [
                        "Save reports to S3"
                    ]
                },
                {
                    "id": "Save reports to S3",
                    "type": "datasource.write.amazon.s3",
                    "params": {
                            "bucketName": "$gigyaS3BucketName",
                            "accessKey": "$gigyaS3Key",
                            "secretKey": "$gigyaS3SecretKey",
                            "objectKeyPrefix": "$gigyaS3ResultPath"
                    }
                },
                {
                    "id": "Record loaded data",
                    "type": "file.format.json",
                    "params": {
                        "fileName": "success_\${now}_\${jobId}.json",
                        "maxFileSize": 5000,
                        "createEmptyFile": false
                    },
                    "next": [
                        "Save reports to S3"   
                    ]
                },
                {
                    "id": "Create matched report",
                    "type": "file.format.json",
                    "params": {
                        "fileName": "matched_\${now}_\${jobId}.json",
                        "maxFileSize": 5000,
                        "createEmptyFile": false
                    },
                    "next": [
                        "Save reports to S3"
                    ]
                },
                {
                    "id": "Create unmatched report",
                    "type": "file.format.json",
                    "params": {
                        "fileName": "unmatched_\${now}_\${jobId}.json",
                        "maxFileSize": 5000,
                        "createEmptyFile": false
                    },
                    "next": [
                        "Save reports to S3"
                    ]
                }
            ]
STEPS;

        return json_decode($steps, false);
    }

    private function setPrivateFields($json)
    {
        $steps = json_decode($json);
        $ids = [
            'Download json from S3',
            'Save reports to S3'
        ];

        foreach ($steps as $step) {
            if (in_array($step->id, $ids)) {
                $step->params->bucketName = GIGYA_S3_BUCKET;
                $step->params->accessKey = GIGYA_S3_KEY;
                $step->params->secretKey = GIGYA_S3_SECRET;
            }

            if ($step->id === 'Download json from S3') {
                $step->params->objectKeyPrefix = GIGYA_S3_UPLOAD_PATH . "$this->currentFile";
            }

            if ($step->id === 'Save reports to S3') {
                $step->params->objectKeyPrefix = GIGYA_S3_RESULT_PATH;
            }
        }

        return $steps;
    }

    // GCAP-541 Campion added by machua/mtanada 20191004
    public function getLiteSteps()
    {
        $gigyaS3Key = GIGYA_S3_KEY;
        $gigyaS3SecretKey = GIGYA_S3_SECRET;
        $gigyaS3ResultPath = GIGYA_S3_RESULT_PATH;
        $gigyaS3BucketName = GIGYA_S3_BUCKET;
        $gigyaUploadFullPathLite = GIGYA_S3_UPLOAD_PATH_LITE;
        $gigyaApiKey = GIGYA_API_KEY;
        $gigyaUserKey = GIGYA_USER_KEY;
        $gigyaSecretKey = GIGYA_SECRET_KEY;

        $steps = <<<STEPS
            [
               {
                  "id":"parse JSON",
                  "type":"file.parse.json",
                  "params":{
                     "addFilename":false
                  },
                  "next":[
                     "injectJobId"
                  ]
               },
               {
                  "id":"Import Lite Account",
                  "type":"datasource.write.gigya.generic",
                  "params":{
                     "apiMethod":"accounts.importLiteAccount",
                     "maxConnections":20,
                     "apiParams":[
                        {
                           "sourceField":"profile",
                           "paramName":"profile",
                           "value":""
                        },
                        {
                           "sourceField":"data",
                           "paramName":"data",
                           "value":""
                        },
                        {
                           "sourceField":"email",
                           "paramName":"email",
                           "value":""
                        },
                        {
                           "sourceField":"UID",
                           "paramName":"UID",
                           "value":""
                        }
                     ],
                     "addResponse":false,
                     "apiKey":"$gigyaApiKey",
                     "userKey":"$gigyaUserKey",
                     "secret":"$gigyaSecretKey"
                  },
                  "next":[],
                  "error":[
                     "Format Error File"
                  ]
               },
               {
                  "id":"Format Error File",
                  "type":"file.format.json",
                  "params":{
                     "fileName":"import_lite_accounts_errors_\${now}.json",
                     "maxFileSize":5000,
                     "createEmptyFile":false
                  },
                  "next":[
                     "Save errors to s3"
                  ]
               },
               {
                  "id":"injectJobId",
                  "type":"field.add",
                  "params":{
                     "fields":[
                        {
                           "field":"data.idxImportJobID",
                           "value":"\${jobId}"
                        }
                     ]
                  },
                  "next":[
                     "Import Lite Account"
                  ]
               },
               {
                  "id":"Read from S3",
                  "type":"datasource.read.amazon.s3",
                  "params":{
                     "bucketName":"$gigyaS3BucketName",
                     "accessKey":"$gigyaS3Key",
                     "secretKey":"$gigyaS3SecretKey",
                     "objectKeyPrefix":"$gigyaUploadFullPathLite"
                  },
                  "next":[
                     "parse JSON"
                  ]
               },
               {
                  "id":"Save errors to s3",
                  "type":"datasource.write.amazon.s3",
                  "params":{
                     "bucketName":"$gigyaS3BucketName",
                     "accessKey":"$gigyaS3Key",
                     "secretKey":"$gigyaS3SecretKey",
                     "objectKeyPrefix":"$gigyaS3ResultPath"
                  }
               }
            ]
STEPS;

        return json_decode($steps, false);
    }

    public function getResetPasswordSteps()
    {
        $gigyaS3Key = GIGYA_S3_KEY;
        $gigyaS3SecretKey = GIGYA_S3_SECRET;
        $gigyaS3BucketName = GIGYA_S3_BUCKET;
        $gigyaUploadFullPathResetPassword = GIGYA_S3_UPLOAD_PATH_RESET_PASSWORD;
        $gigyaApiKey = GIGYA_API_KEY;
        $gigyaUserKey = GIGYA_USER_KEY;
        $gigyaSecretKey = GIGYA_SECRET_KEY;

        $gigyaResultPath = GIGYA_S3_RESULT_PATH_RESET_PASSWORD;

        $steps = <<<STEPS
        [
            {
                "id": "GO_requests",
                "type": "datasource.read.amazon.s3",
                "params": {
                    "bucketName": "$gigyaS3BucketName",
                    "accessKey": "$gigyaS3Key",
                    "secretKey": "$gigyaS3SecretKey",
                    "objectKeyPrefix": "$gigyaUploadFullPathResetPassword"
                },
                "next": [
                  "parseGOdsv"
                ]
              },
              {
                "id": "lookupGOAccount",
                "type": "datasource.lookup.gigya.account",
                "params": {
                  "select": "UID, loginIDs, preferences, profile, data",
                  "handleFieldConflicts": "take_lookup",
                  "mismatchBehavior": "error",
                  "lookupFields": [
                    {
                      "sourceField": "UID",
                      "gigyaField": "UID"
                    },
                    {
                      "sourceField": "email",
                      "gigyaField": "loginIDs.email"
                    }
                  ],
                  "lookupFieldsOperator": "OR",
                  "matchBehavior": "process",
                  "isCaseSensitive": false,
                  "from": "accounts",
                  "batchSize": 200,
                  "maxConcurrency": 1
                },
                "next": [
                  "Validate user"
                ],
                "error": [
                  "formatGOErrorFile"
                ]
              },
              {
                "id": "parseGOdsv",
                "type": "file.parse.dsv",
                "params": {
                  "columnSeparator": ",",
                  "inferTypes": true,
                  "addFilename": false,
                  "fileCharset": "auto-detect"
                },
                "next": [
                  "Lookup for Lite Accounts"
                ]
              },
              {
                "id": "gigyaGenericWriter (GO)",
                "type": "datasource.write.gigya.generic",
                "params": {
                  "maxConnections": 10,
                  "apiMethod": "accounts.setAccountInfo",
                  "apiParams": [
                    {
                      "sourceField": "UID",
                      "paramName": "UID",
                      "value": ""
                    },
                    {
                      "sourceField": "newPassword",
                      "paramName": "newPassword",
                      "value": ""
                    },
                    {
                      "sourceField": "",
                      "paramName": "securityOverride",
                      "value": "true"
                    }
                  ],
                  "apiKey": "$gigyaApiKey",
                  "userKey": "$gigyaUserKey",
                  "secret": "$gigyaSecretKey",
                  "addResponse": false
                },
                "next": [
                  "file.format.json"
                ],
                "error": [
                  "formatGOErrorFile"
                ]
              },
              {
                "id": "amazon.s3",
                "type": "datasource.write.amazon.s3",
                "params": {
                  "bucketName": "$gigyaS3BucketName",
                  "accessKey": "$gigyaS3Key",
                  "secretKey": "$gigyaS3SecretKey",
                  "objectKeyPrefix": "$gigyaResultPath"
                }
              },
              {
                "id": "file.format.json",
                "type": "file.format.json",
                "params": {
                  "fileName": "success/reset_pw_success_\${jobId}.json",
                  "maxFileSize": 5000,
                  "createEmptyFile": false
                },
                "next": [
                  "amazon.s3"
                ]
              },
              {
                "id": "formatGOErrorFile",
                "type": "file.format.json",
                "params": {
                  "fileName": "error/reset_pw_error_\${jobId}.json",
                  "maxFileSize": 5000,
                  "createEmptyFile": false
                },
                "next": [
                  "amazon.s3"
                ]
              },
              {
                "id": "Validate user",
                "type": "record.evaluate",
                "params": {
                  "script": "ZnVuY3Rpb24gcHJvY2VzcyhyZWNvcmQsIGN0eCwgbG9nZ2VyLCBuZXh0LCBlcnJvcikgewogIHZhciBlcnJvckRldGFpbHMgPSB7CiAgICBlcnJvck1lc3NhZ2U6ICcnLAogIH0KCiAgaWYgKHJlY29yZCAhPT0gbnVsbCkgewogICAgLy8gSW5pdGlhbGl6ZSBlcnJvciBtZXNzYWdlCiAgICBlcnJvckRldGFpbHMuZXJyb3JNZXNzYWdlID0gJ1VJRDonICsgcmVjb3JkLlVJRCArICcuICcKCiAgICB2YXIgbmV3UmVjb3JkID0gewogICAgICBVSUQ6IHJlY29yZC5VSUQsCiAgICAgIGVtYWlsOiByZWNvcmQucHJvZmlsZS5lbWFpbCwKICAgICAgbmV3UGFzc3dvcmQ6IHJlY29yZC5uZXdQYXNzd29yZCwKICAgIH0KICAgIHZhciBtc2cKCiAgICAvLyAxLiBDaGVjayBpZiB1c2VyIGhhcyBtdWx0aXBsZSBsb2dpbklEcy5lbWFpbAogICAgaWYgKHJlY29yZC5sb2dpbklEcy5lbWFpbHMubGVuZ3RoID4gMSkgewogICAgICBtc2cgPSAnVXNlciBoYXMgbW9yZSB0aGFuIG9uZSBhY3RpdmUgZW1haWxzJwogICAgICBlcnJvckRldGFpbHMuZXJyb3JNZXNzYWdlICs9ICdEZXRhaWxzOiAnICsgbXNnCiAgICAgIG5ld1JlY29yZC5fZXJyb3JEZXRhaWxzID0gZXJyb3JEZXRhaWxzCiAgICAgIGVycm9yLmFjY2VwdChuZXdSZWNvcmQsIHJlY29yZCkKICAgICAgcmV0dXJuIG51bGwKICAgIH0KCiAgICAvLyAyLiBDaGVjayBpZiB1c2VyIGJlbG9uZ3MgdG8gJ2VkdWVsdCcgcGxhdGZvcm0KICAgIGlmIChyZWNvcmQuZGF0YS5lZHVlbHQgPT09IG51bGwpIHsKICAgICAgbXNnID0gJ1VzZXIgaGFzIGRhdGEgZnJvbSBvdGhlciBwbGF0Zm9ybXMgb3Igbm90IGEgR28gdXNlcicKICAgICAgZXJyb3JEZXRhaWxzLmVycm9yTWVzc2FnZSArPSAnRGV0YWlsczogJyArIG1zZwogICAgICBuZXdSZWNvcmQuX2Vycm9yRGV0YWlscyA9IGVycm9yRGV0YWlscwogICAgICBlcnJvci5hY2NlcHQobmV3UmVjb3JkLCByZWNvcmQpCiAgICAgIHJldHVybiBudWxsCiAgICB9CgogICAgLy8gMy4gQ2hlY2sgaWYgdXNlciBoYXMgYSAnc3lzdGVtSURzJyBhdHRyaWJ1dGUKICAgIGlmIChyZWNvcmQuZGF0YS5zeXN0ZW1JRHMgIT09IG51bGwpIHsKICAgICAgLy8gNC4gQ2hlY2sgaWYgdXNlciBoYXMgYW4gaWRUeXBlIG9mICdHTycgb3IgJ2dvJwogICAgICBmb3IgKHZhciBpID0gMDsgaSA8IHJlY29yZC5kYXRhLnN5c3RlbUlEcy5sZW5ndGg7IGkrKykgewogICAgICAgIHZhciBzeXN0ZW1JRCA9IHJlY29yZC5kYXRhLnN5c3RlbUlEc1tpXQogICAgICAgIGlmIChzeXN0ZW1JRC5pZFR5cGUudG9Mb3dlckNhc2UoKSAhPT0gJ2dvJykgewogICAgICAgICAgbXNnID0gJ1VzZXIgaGFzIGRhdGEgZnJvbSBvdGhlciBwbGF0Zm9ybXMgb3Igbm90IGEgR28gdXNlcicKICAgICAgICAgIGVycm9yRGV0YWlscy5lcnJvck1lc3NhZ2UgKz0gJ0RldGFpbHM6ICcgKyBtc2cKICAgICAgICAgIG5ld1JlY29yZC5fZXJyb3JEZXRhaWxzID0gZXJyb3JEZXRhaWxzCiAgICAgICAgICBlcnJvci5hY2NlcHQobmV3UmVjb3JkLCByZWNvcmQpCiAgICAgICAgICByZXR1cm4gbnVsbAogICAgICAgIH0KICAgICAgfQogICAgfQoKICAgIC8vIDUuIENoZWNrIGlmIHVzZXIgaGFzIG90aGVyIGRhdGEuKiBhdHRyaWJ1dGUKICAgIGZvciAodmFyIHByb3BlcnR5IGluIHJlY29yZC5kYXRhKSB7CiAgICAgIGlmICgKICAgICAgICBwcm9wZXJ0eSAhPT0gJ2VkdWVsdCcgJiYKICAgICAgICBwcm9wZXJ0eSAhPT0gJ3N5c3RlbUlEcycgJiYKICAgICAgICBwcm9wZXJ0eSAhPT0gJ2lkeEltcG9ydEpvYklEJyAmJgogICAgICAgIHByb3BlcnR5ICE9PSAnY2xhaW1zJwogICAgICApIHsKICAgICAgICBtc2cgPSAnVXNlciBoYXMgZGF0YSBmcm9tIG90aGVyIHBsYXRmb3JtcyBvciBub3QgYSBHbyB1c2VyJwogICAgICAgIGVycm9yRGV0YWlscy5lcnJvck1lc3NhZ2UgKz0gJ0RldGFpbHM6ICcgKyBtc2cKICAgICAgICBuZXdSZWNvcmQuX2Vycm9yRGV0YWlscyA9IGVycm9yRGV0YWlscwogICAgICAgIGVycm9yLmFjY2VwdChuZXdSZWNvcmQsIHJlY29yZCkKICAgICAgICByZXR1cm4gbnVsbAogICAgICB9CiAgICB9CgogICAgLy8gQWRkZW5kdW06IENoZWNrIGlmIHVzZXIgYWNjZXB0ZWQgR2xvYmFsIEdvIHRlcm1zCiAgICBpZiAocmVjb3JkLnByZWZlcmVuY2VzICE9PSBudWxsKSB7CiAgICAgIGlmIChyZWNvcmQucHJlZmVyZW5jZXMudGVybXMgIT09IG51bGwpIHsKICAgICAgICBmb3IgKHZhciB0ZXJtcyBpbiByZWNvcmQucHJlZmVyZW5jZXMudGVybXMpIHsKICAgICAgICAgIGlmICh0ZXJtcy50b0xvd2VyQ2FzZSgpICE9PSAnZ28nICYmIHRlcm1zLnRvTG93ZXJDYXNlKCkgIT09ICdodWInKSB7CiAgICAgICAgICAgIG1zZyA9ICdVc2VyIGhhcyBkYXRhIGZyb20gb3RoZXIgcGxhdGZvcm1zIG9yIG5vdCBhIEdvIHVzZXInCiAgICAgICAgICAgIGVycm9yRGV0YWlscy5lcnJvck1lc3NhZ2UgKz0gJ0RldGFpbHM6ICcgKyBtc2cKICAgICAgICAgICAgbmV3UmVjb3JkLl9lcnJvckRldGFpbHMgPSBlcnJvckRldGFpbHMKICAgICAgICAgICAgZXJyb3IuYWNjZXB0KG5ld1JlY29yZCwgcmVjb3JkKQogICAgICAgICAgICByZXR1cm4gbnVsbAogICAgICAgICAgfSBlbHNlIHsKICAgICAgICAgICAgaWYgKCFyZWNvcmQucHJlZmVyZW5jZXMudGVybXNbdGVybXNdKSB7CiAgICAgICAgICAgICAgbXNnID0gJ1VzZXIgaGFzIGRhdGEgZnJvbSBvdGhlciBwbGF0Zm9ybXMgb3Igbm90IGEgR28gdXNlcicKICAgICAgICAgICAgICBlcnJvckRldGFpbHMuZXJyb3JNZXNzYWdlICs9ICdEZXRhaWxzOiAnICsgbXNnCiAgICAgICAgICAgICAgbmV3UmVjb3JkLl9lcnJvckRldGFpbHMgPSBlcnJvckRldGFpbHMKICAgICAgICAgICAgICBlcnJvci5hY2NlcHQobmV3UmVjb3JkLCByZWNvcmQpCiAgICAgICAgICAgICAgcmV0dXJuIG51bGwKICAgICAgICAgICAgfQogICAgICAgICAgfQogICAgICAgIH0KICAgICAgfQogICAgfQogIH0KCiAgcmV0dXJuIHJlY29yZAp9Cg==",
                  "notifyLastRecord": true,
                  "ECMAScriptVersion": "5.1"
                },
                "next": [
                  "gigyaGenericWriter (GO)"
                ],
                "error": [
                  "formatGOErrorFile"
                ]
              },
              {
                "id": "Lookup for Lite Accounts",
                "type": "datasource.lookup.gigya.account",
                "params": {
                  "select": "UID, email, hasLiteAccount, hasFullAccount, profile",
                  "handleFieldConflicts": "take_lookup",
                  "mismatchBehavior": "error",
                  "lookupFields": [
                    {
                      "sourceField": "email",
                      "gigyaField": "email"
                    },
                    {
                      "sourceField": "firstName",
                      "gigyaField": "profile.firstName"
                    },
                    {
                      "sourceField": "lastName",
                      "gigyaField": "profile.lastName"
                    }
                  ],
                  "lookupFieldsOperator": "AND",
                  "matchBehavior": "process",
                  "isCaseSensitive": false,
                  "from": "emailAccounts",
                  "batchSize": 200,
                  "maxConcurrency": 1
                },
                "next": [
                  "Validate if Lite Account"
                ],
                "error": [
                  "formatGOErrorFile"
                ]
              },
              {
                "id": "Validate if Lite Account",
                "type": "record.evaluate",
                "params": {
                  "script": "Ly8gUmVjb3JkLmV2YWx1YXRlDQpmdW5jdGlvbiBwcm9jZXNzKHJlY29yZCwgY3R4LCBsb2dnZXIsIG5leHQsIGVycm9yKSB7DQogIHZhciBlcnJvckRldGFpbHMgPSB7DQogICAgImVycm9yTWVzc2FnZSIgOiAiIg0KICB9Ow0KICANCiAgaWYgKHJlY29yZCAhPT0gbnVsbCkgew0KICAgIC8vIEluaXRpYWxpemUgZXJyb3IgbWVzc2FnZQ0KICAgIGVycm9yRGV0YWlscy5lcnJvck1lc3NhZ2UgPSAiVUlEOiIgKyByZWNvcmQuVUlEICsgIi4gIjsNCiAgICB2YXIgbmV3UmVjb3JkID0gew0KICAgICAgIlVJRCI6IHJlY29yZC5VSUQsDQogICAgICAiZW1haWwiOiByZWNvcmQucHJvZmlsZS5lbWFpbCB8fCByZWNvcmQuZW1haWwNCiAgICB9Ow0KDQogICAgdmFyIG1zZzsNCg0KICAgIGlmIChyZWNvcmQuaGFzTGl0ZUFjY291bnQgJiYgIXJlY29yZC5oYXNGdWxsQWNjb3VudCkgew0KICAgICAgbXNnID0gIlVzZXIgaGFzIGEgTGl0ZSBhY2NvdW50IjsNCiAgICAgIGVycm9yRGV0YWlscy5lcnJvck1lc3NhZ2UgKz0gIkRldGFpbHM6ICIgKyBtc2c7DQogICAgICBuZXdSZWNvcmQuX2Vycm9yRGV0YWlscyA9IGVycm9yRGV0YWlsczsNCiAgICAgIGVycm9yLmFjY2VwdChuZXdSZWNvcmQsIHJlY29yZCk7DQogICAgICByZXR1cm4gbnVsbDsNCiAgICB9DQogIH0NCg0KICByZXR1cm4gcmVjb3JkOw0KfQ0K",
                  "notifyLastRecord": true,
                  "ECMAScriptVersion": "5.1"
                },
                "next": [
                  "lookupGOAccount"
                ],
                "error": [
                  "formatGOErrorFile"
                ]
              }
            ]
STEPS;

        return json_decode($steps, false);
    }

    public function getBulkDeleteSteps()
    {
        $gigyaS3Key = GIGYA_S3_KEY;
        $gigyaS3SecretKey = GIGYA_S3_SECRET;
        $gigyaS3BucketName = GIGYA_S3_BUCKET;

        $gigyaApiKey = GIGYA_API_KEY;
        $gigyaUserKey = GIGYA_USER_KEY;
        $gigyaSecretKey = GIGYA_SECRET_KEY;

        $gigyaUploadFullPathBulkDelete = GIGYA_S3_UPLOAD_PATH_BULK_DELETE;
        $gigyaBulkDeleteResultsBucket = GIGYA_S3_BULK_DELETE_RESULTS_BUCKET;
        $gigyaBulkDeleteSuccessResultPath = GIGYA_S3_SUCCESS_RESULT_PATH_BULK_DELETE;
        $gigyaBulkDeleteErrorResultPath = GIGYA_S3_ERROR_RESULT_PATH_BULK_DELETE;


        $steps = <<<STEPS
        [
		{
			"id": "Read S3",
			"type": "datasource.read.amazon.s3",
			"params": {
				"bucketName": "$gigyaS3BucketName",
				"accessKey": "$gigyaS3Key",
				"secretKey": "$gigyaS3SecretKey",
				"objectKeyPrefix": "$gigyaUploadFullPathBulkDelete"
			},
			"next": [
				"parse csv"
			]
		},
		{
			"id": "parse csv",
			"type": "file.parse.dsv",
			"params": {
				"columnSeparator": ",",
				"inferTypes": true,
				"addFilename": false
			},
			"next": [
				"gigya.account",
				"Lookup Lite Accounts"
			],
			"error": []
		},
		{
			"id": "gigya.account",
			"type": "datasource.lookup.gigya.account",
			"params": {
				"select": "*",
				"handleFieldConflicts": "take_lookup",
				"mismatchBehavior": "error",
				"lookupFields": [
					{
						"sourceField": "email",
						"gigyaField": "profile.email"
					},
					{
						"sourceField": "firstName",
						"gigyaField": "profile.firstName"
					},
					{
						"sourceField": "lastName",
						"gigyaField": "profile.lastName"
					}
				],
				"lookupFieldsOperator": "AND",
				"matchBehavior": "process",
				"isCaseSensitive": false,
				"from": "accounts",
				"batchSize": 200,
				"maxConcurrency": 1
			},
			"next": [
				"Validations"
			],
			"error": [
				"Format error"
			]
		},
		{
			"id": "Validations",
			"type": "record.evaluate",
			"params": {
				"script": "ZnVuY3Rpb24gcHJvY2VzcyhyZWNvcmQsIGN0eCwgbG9nZ2VyLCBuZXh0LCBlcnJvcikgewogIHZhciBlcnJvckRldGFpbHMgPSB7CiAgICBlcnJvck1lc3NhZ2U6ICcnLAogIH0KCiAgaWYgKHJlY29yZCAhPT0gbnVsbCkgewogICAgLy8gSW5pdGlhbGl6ZSBlcnJvciBtZXNzYWdlCiAgICBlcnJvckRldGFpbHMuZXJyb3JNZXNzYWdlID0gJ1VJRDonICsgcmVjb3JkLlVJRCArICcuICcKCiAgICB2YXIgbmV3UmVjb3JkID0gewogICAgICBVSUQ6IHJlY29yZC5VSUQsCiAgICAgIGVtYWlsOiByZWNvcmQucHJvZmlsZS5lbWFpbCwKICAgICAgbmV3UGFzc3dvcmQ6IHJlY29yZC5uZXdQYXNzd29yZCwKICAgIH0KICAgIHZhciBtc2cKCiAgICAvLyAxLiBDaGVjayBpZiB1c2VyIGhhcyBtdWx0aXBsZSBsb2dpbklEcy5lbWFpbAogICAgaWYgKHJlY29yZC5sb2dpbklEcy5lbWFpbHMubGVuZ3RoID4gMSkgewogICAgICBtc2cgPSAnVXNlciBoYXMgbW9yZSB0aGFuIG9uZSBhY3RpdmUgZW1haWxzJwogICAgICBlcnJvckRldGFpbHMuZXJyb3JNZXNzYWdlICs9ICdEZXRhaWxzOiAnICsgbXNnCiAgICAgIG5ld1JlY29yZC5fZXJyb3JEZXRhaWxzID0gZXJyb3JEZXRhaWxzCiAgICAgIGVycm9yLmFjY2VwdChuZXdSZWNvcmQsIHJlY29yZCkKICAgICAgcmV0dXJuIG51bGwKICAgIH0KCiAgICAvLyAyLiBDaGVjayBpZiB1c2VyIGJlbG9uZ3MgdG8gJ2VkdWVsdCcgcGxhdGZvcm0KICAgIGlmIChyZWNvcmQuZGF0YS5lZHVlbHQgPT09IG51bGwpIHsKICAgICAgbXNnID0gJ1VzZXIgaGFzIGRhdGEgZnJvbSBvdGhlciBwbGF0Zm9ybXMgb3Igbm90IGEgR28gdXNlcicKICAgICAgZXJyb3JEZXRhaWxzLmVycm9yTWVzc2FnZSArPSAnRGV0YWlsczogJyArIG1zZwogICAgICBuZXdSZWNvcmQuX2Vycm9yRGV0YWlscyA9IGVycm9yRGV0YWlscwogICAgICBlcnJvci5hY2NlcHQobmV3UmVjb3JkLCByZWNvcmQpCiAgICAgIHJldHVybiBudWxsCiAgICB9CgogICAgLy8gMy4gQ2hlY2sgaWYgdXNlciBoYXMgYSAnc3lzdGVtSURzJyBhdHRyaWJ1dGUKICAgIGlmIChyZWNvcmQuZGF0YS5zeXN0ZW1JRHMgIT09IG51bGwpIHsKICAgICAgLy8gNC4gQ2hlY2sgaWYgdXNlciBoYXMgYW4gaWRUeXBlIG9mICdHTycgb3IgJ2dvJwogICAgICBmb3IgKHZhciBpID0gMDsgaSA8IHJlY29yZC5kYXRhLnN5c3RlbUlEcy5sZW5ndGg7IGkrKykgewogICAgICAgIHZhciBzeXN0ZW1JRCA9IHJlY29yZC5kYXRhLnN5c3RlbUlEc1tpXQogICAgICAgIGlmIChzeXN0ZW1JRC5pZFR5cGUudG9Mb3dlckNhc2UoKSAhPT0gJ2dvJykgewogICAgICAgICAgbXNnID0gJ1VzZXIgaGFzIGRhdGEgZnJvbSBvdGhlciBwbGF0Zm9ybXMgb3Igbm90IGEgR28gdXNlcicKICAgICAgICAgIGVycm9yRGV0YWlscy5lcnJvck1lc3NhZ2UgKz0gJ0RldGFpbHM6ICcgKyBtc2cKICAgICAgICAgIG5ld1JlY29yZC5fZXJyb3JEZXRhaWxzID0gZXJyb3JEZXRhaWxzCiAgICAgICAgICBlcnJvci5hY2NlcHQobmV3UmVjb3JkLCByZWNvcmQpCiAgICAgICAgICByZXR1cm4gbnVsbAogICAgICAgIH0KICAgICAgfQogICAgfQoKICAgIC8vIDUuIENoZWNrIGlmIHVzZXIgaGFzIG90aGVyIGRhdGEuKiBhdHRyaWJ1dGUKICAgIGZvciAodmFyIHByb3BlcnR5IGluIHJlY29yZC5kYXRhKSB7CiAgICAgIGlmICgKICAgICAgICBwcm9wZXJ0eSAhPT0gJ2VkdWVsdCcgJiYKICAgICAgICBwcm9wZXJ0eSAhPT0gJ3N5c3RlbUlEcycgJiYKICAgICAgICBwcm9wZXJ0eSAhPT0gJ2lkeEltcG9ydEpvYklEJwogICAgICApIHsKICAgICAgICBtc2cgPSAnVXNlciBoYXMgZGF0YSBmcm9tIG90aGVyIHBsYXRmb3JtcyBvciBub3QgYSBHbyB1c2VyJwogICAgICAgIGVycm9yRGV0YWlscy5lcnJvck1lc3NhZ2UgKz0gJ0RldGFpbHM6ICcgKyBtc2cKICAgICAgICBuZXdSZWNvcmQuX2Vycm9yRGV0YWlscyA9IGVycm9yRGV0YWlscwogICAgICAgIGVycm9yLmFjY2VwdChuZXdSZWNvcmQsIHJlY29yZCkKICAgICAgICByZXR1cm4gbnVsbAogICAgICB9CiAgICB9CgogICAgLy8gQWRkZW5kdW06IENoZWNrIGlmIHVzZXIgYWNjZXB0ZWQgR2xvYmFsIEdvIHRlcm1zCiAgICBpZiAocmVjb3JkLnByZWZlcmVuY2VzICE9PSBudWxsKSB7CiAgICAgIGlmIChyZWNvcmQucHJlZmVyZW5jZXMudGVybXMgIT09IG51bGwpIHsKICAgICAgICBmb3IgKHZhciB0ZXJtcyBpbiByZWNvcmQucHJlZmVyZW5jZXMudGVybXMpIHsKICAgICAgICAgIGlmICh0ZXJtcy50b0xvd2VyQ2FzZSgpICE9PSAnZ28nICYmIHRlcm1zLnRvTG93ZXJDYXNlKCkgIT09ICdodWInKSB7CiAgICAgICAgICAgIG1zZyA9ICdVc2VyIGhhcyBkYXRhIGZyb20gb3RoZXIgcGxhdGZvcm1zIG9yIG5vdCBhIEdvIHVzZXInCiAgICAgICAgICAgIGVycm9yRGV0YWlscy5lcnJvck1lc3NhZ2UgKz0gJ0RldGFpbHM6ICcgKyBtc2cKICAgICAgICAgICAgbmV3UmVjb3JkLl9lcnJvckRldGFpbHMgPSBlcnJvckRldGFpbHMKICAgICAgICAgICAgZXJyb3IuYWNjZXB0KG5ld1JlY29yZCwgcmVjb3JkKQogICAgICAgICAgICByZXR1cm4gbnVsbAogICAgICAgICAgfSBlbHNlIHsKICAgICAgICAgICAgaWYgKCFyZWNvcmQucHJlZmVyZW5jZXMudGVybXNbdGVybXNdKSB7CiAgICAgICAgICAgICAgbXNnID0gJ1VzZXIgaGFzIGRhdGEgZnJvbSBvdGhlciBwbGF0Zm9ybXMgb3Igbm90IGEgR28gdXNlcicKICAgICAgICAgICAgICBlcnJvckRldGFpbHMuZXJyb3JNZXNzYWdlICs9ICdEZXRhaWxzOiAnICsgbXNnCiAgICAgICAgICAgICAgbmV3UmVjb3JkLl9lcnJvckRldGFpbHMgPSBlcnJvckRldGFpbHMKICAgICAgICAgICAgICBlcnJvci5hY2NlcHQobmV3UmVjb3JkLCByZWNvcmQpCiAgICAgICAgICAgICAgcmV0dXJuIG51bGwKICAgICAgICAgICAgfQogICAgICAgICAgfQogICAgICAgIH0KICAgICAgfQogICAgfQogIH0KCiAgcmV0dXJuIHJlY29yZAp9Cg==",
				"notifyLastRecord": true
			},
			"next": [
				"Deactivate Account"
			],
			"error": [
				"Format error"
			]
		},
		{
			"id": "Format error",
			"type": "file.format.json",
			"params": {
				"fileName": "error_delete_\${jobId}.json",
				"maxFileSize": 5000,
				"createEmptyFile": false
			},
			"next": [
				"Write error results to S3"
			]
		},
		{
			"id": "Delete Account",
			"type": "datasource.write.gigya.generic",
			"params": {
				"apiMethod": "accounts.deleteAccount",
				"maxConnections": 10,
				"apiKey": "$gigyaApiKey",
				"userKey": "$gigyaUserKey",
				"secret": "$gigyaSecretKey",
				"apiParams": [
					{
						"sourceField": "UID",
						"paramName": "UID",
						"value": ""
					}
				],
				"addResponse": false
			},
			"next": [
				"Format Success"
			],
			"error": [
				"Format error"
			]
		},
		{
			"id": "Format Success",
			"type": "file.format.json",
			"params": {
				"fileName": "success_delete_\${jobId}.json",
				"maxFileSize": 5000,
				"createEmptyFile": false
			},
			"next": [
				"Write success results to S3"
			],
			"error": []
		},
		{
			"id": "Write error results to S3",
			"type": "datasource.write.amazon.s3",
			"params": {
				"bucketName": "$gigyaBulkDeleteResultsBucket",
				"accessKey": "$gigyaS3Key",
				"secretKey": "$gigyaS3SecretKey",
				"objectKeyPrefix": "$gigyaBulkDeleteErrorResultPath"
			}
		},
		{
			"id": "Deactivate Account",
			"type": "datasource.write.gigya.generic",
			"params": {
				"apiMethod": "accounts.setAccountInfo",
				"maxConnections": 10,
				"apiParams": [
					{
						"sourceField": "",
						"paramName": "isActive",
						"value": "false"
					},
					{
						"sourceField": "UID",
						"paramName": "UID",
						"value": ""
					}
				],
				"apiKey": "$gigyaApiKey",
				"userKey": "$gigyaUserKey",
				"secret": "$gigyaSecretKey",
				"addResponse": false
			},
			"next": [
				"Add deleted field"
			],
			"error": [
				"Format error"
			]
		},
		{
			"id": "Add deleted field",
			"type": "field.add",
			"params": {
				"fields": [
					{
						"field": "deletedUser",
						"value": "true"
					}
				]
			},
			"next": [
				"Delete Account"
			],
			"error": [
				"Format error"
			]
		},
		{
			"id": "Lookup Lite Accounts",
			"type": "datasource.lookup.gigya.account",
			"params": {
				"select": "UID, email, hasLiteAccount, hasFullAccount, profile, token",
				"handleFieldConflicts": "take_lookup",
				"mismatchBehavior": "error",
				"lookupFields": [
					{
						"sourceField": "email",
						"gigyaField": "email"
					},
					{
						"sourceField": "firstName",
						"gigyaField": "profile.firstName"
					},
					{
						"sourceField": "lastName",
						"gigyaField": "profile.lastName"
					}
				],
				"lookupFieldsOperator": "AND",
				"matchBehavior": "process",
				"isCaseSensitive": false,
				"from": "emailAccounts",
				"batchSize": 200,
				"maxConcurrency": 1
			},
			"next": [
				"Validate lite account"
			],
			"error": [
				"Format error"
			]
		},
		{
			"id": "Delete Lite Account",
			"type": "datasource.write.gigya.generic",
			"params": {
				"apiMethod": "accounts.deleteLiteAccount",
				"maxConnections": 10,
				"apiKey": "$gigyaApiKey",
				"userKey": "$gigyaUserKey",
				"secret": "$gigyaSecretKey",
				"apiParams": [
					{
						"sourceField": "UID",
						"paramName": "UID",
						"value": ""
					},
					{
						"sourceField": "token",
						"paramName": "emailAccountToken",
						"value": ""
					}
				],
				"addResponse": false
			},
			"next": [
				"Format Success"
			],
			"error": [
				"Format error"
			]
		},
		{
			"id": "Add deleted field to lite account",
			"type": "field.add",
			"params": {
				"fields": [
					{
						"field": "deletedUser",
						"value": "true"
					}
				]
			},
			"next": [
				"Delete Lite Account"
			],
			"error": [
				"Format error"
			]
		},
		{
			"id": "Validate lite account",
			"type": "record.evaluate",
			"params": {
				"script": "Ly8gUmVjb3JkLmV2YWx1YXRlDQpmdW5jdGlvbiBwcm9jZXNzKHJlY29yZCwgY3R4LCBsb2dnZXIsIG5leHQsIGVycm9yKSB7DQogIHZhciBlcnJvckRldGFpbHMgPSB7DQogICAgImVycm9yTWVzc2FnZSIgOiAiIg0KICB9Ow0KICANCiAgaWYgKHJlY29yZCAhPT0gbnVsbCkgew0KICAgIC8vIEluaXRpYWxpemUgZXJyb3IgbWVzc2FnZQ0KICAgIGVycm9yRGV0YWlscy5lcnJvck1lc3NhZ2UgPSAiVUlEOiIgKyByZWNvcmQuVUlEICsgIi4gIjsNCiAgICB2YXIgbmV3UmVjb3JkID0gew0KICAgICAgIlVJRCI6IHJlY29yZC5VSUQsDQogICAgICAiZW1haWwiOiByZWNvcmQucHJvZmlsZS5lbWFpbCB8fCByZWNvcmQuZW1haWwsDQogICAgICAiZW1haWxBY2NvdW50VG9rZW4iOiByZWNvcmQudG9rZW4NCiAgICB9Ow0KDQogICAgdmFyIG1zZzsNCg0KICAgIGlmIChyZWNvcmQuaGFzTGl0ZUFjY291bnQgJiYgIXJlY29yZC5oYXNGdWxsQWNjb3VudCkgew0KICAgICAgcmV0dXJuIHJlY29yZDsNCiAgICB9DQogIH0NCn=",
				"notifyLastRecord": false
			},
			"next": [
				"Add deleted field to lite account"
			],
			"error": []
		},
		{
			"id": "Write success results to S3",
			"type": "datasource.write.amazon.s3",
			"params": {
				"bucketName": "$gigyaBulkDeleteResultsBucket",
				"accessKey": "$gigyaS3Key",
				"secretKey": "$gigyaS3SecretKey",
				"objectKeyPrefix": "$gigyaBulkDeleteSuccessResultPath"
			}
		}
	]
STEPS;
        return json_decode($steps, false);
    }
}
