<?php
namespace ExtensionsModel;

require_once __DIR__ . '/../../../models/base.php';

class HostingFeatureCompanyModel extends \Model\BaseModel
{
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'ext_hosting_feature_company';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return [
            ['hosting_feature_id', 'required'],
        ];
    }

    /**
     * @return array
     */
    public function getData($data = null)
    {
        $sql = 'SELECT t.*, f.title AS original_title, f.description, a.name AS admin_name  
            FROM {tablePrefix}ext_hosting_feature_company t
            LEFT JOIN {tablePrefix}ext_hosting_feature f ON f.id = t.hosting_feature_id
            LEFT JOIN {tablePrefix}admin a ON a.id = t.created_by 
            WHERE 1';

        $params = [];
        if (is_array($data)) {
            if (!empty($data['hosting_company_id'])) {
                $sql .= ' AND t.hosting_company_id=:hosting_company_id';
                $params['hosting_company_id'] = $data['hosting_company_id'];
            }
            if (!empty($data['hosting_feature_id'])) {
                $sql .= ' AND t.hosting_feature_id=:hosting_feature_id';
                $params['hosting_feature_id'] = $data['hosting_feature_id'];
            }
        }

        $sql .= ' ORDER BY t.created_at DESC';

        $sql = str_replace(['{tablePrefix}'], [$this->_tbl_prefix], $sql);

        $rows = \Model\R::getAll( $sql, $params );

        return $rows;
    }

    /**
     * @param $id
     * @return array
     */
    public function getDetail($id)
    {
        $sql = 'SELECT t.*, c.title AS original_title, c.description, a.name AS created_by_name, ab.name AS updated_by_name 
            FROM {tablePrefix}ext_hosting_feature_company t 
            LEFT JOIN {tablePrefix}ext_hosting_feature c ON c.id = t.hosting_feature_id 
            LEFT JOIN {tablePrefix}admin a ON a.id = t.created_by 
            LEFT JOIN {tablePrefix}admin ab ON ab.id = t.updated_by 
            WHERE t.id =:id';

        $sql = str_replace(['{tablePrefix}'], [$this->_tbl_prefix], $sql);

        $row = \Model\R::getRow( $sql, ['id'=>$id] );

        return $row;
    }
}
