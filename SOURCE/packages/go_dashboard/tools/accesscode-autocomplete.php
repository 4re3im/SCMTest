<?php

/**
 * Class SearchAccessCode
 *
 * @author jsunico@cambridge.org
 */

class SearchAccessCode
{
    const KEYWORD = 'keyword';
    const PROOF = 'proof';

    public static function searchFromDatabase($keyword)
    {
        $db = Loader::db();
        $query = "
            SELECT
                ac.ID as id, ac.AccessCode as value, ac.AccessCode as label
            FROM CupGoAccessCodes ac
            WHERE ac.AccessCode LIKE ?;";
        return $db->GetAll($query, ["%$keyword%"]);
    }

    public static function searchFromHub($keyword)
    {
        Loader::library('hub-sdk/autoload');
        $paramKey = static::KEYWORD;
        if (self::isAccessCodeComplete($keyword)) {
            $paramKey = static::PROOF;
        }
        $searchParams = [
            $paramKey => $keyword
        ];
        $permissions = \HubEntitlement\Models\Permission::where($searchParams);

        return array_map(function ($permission) {
            return [
                'id' => $permission->id,
                'value' => $permission->proof,
                'label' => $permission->proof
            ];
        }, $permissions);
    }

    public static function isAccessCodeComplete($keyword)
    {
        $pattern = '/^([a-zA-Z0-9]{4}-){3}[a-zA-Z0-9]{4}?$/';
        return preg_match($pattern, $keyword);
    }
}

if (!filter_input(INPUT_GET, 'term')) {
    return;
}


echo json_encode(
    SearchAccessCode::searchFromHub(filter_input(INPUT_GET, 'term'))
);
