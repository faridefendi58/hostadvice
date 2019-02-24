<?php
namespace ExtensionsModel;

require_once __DIR__ . '/../../../models/base.php';

class HostingServerLocationModel extends \Model\BaseModel
{
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'ext_hosting_server_location';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return [
            ['hosting_company_id', 'required'],
        ];
    }

    /**
     * @return array
     */
    public function getData($data = null)
    {
        $sql = 'SELECT t.*, c.code AS country_code, c.title AS country_name, cp.title AS company_name
            FROM {tablePrefix}ext_hosting_server_location t 
            LEFT JOIN {tablePrefix}country c ON c.id = t.country_id 
            LEFT JOIN {tablePrefix}ext_hosting_company cp ON cp.id = t.hosting_company_id 
            WHERE 1';

        $params = [];
        if (is_array($data)) {
            if (!empty($data['hosting_company_id'])) {
                $sql .= ' AND t.hosting_company_id=:hosting_company_id';
                $params['hosting_company_id'] = $data['hosting_company_id'];
            }
        }

        $sql .= ' ORDER BY t.created_at DESC';

        if (isset($data['limit'])) {
            $sql .= ' LIMIT '. $data['limit'];
        }

        $sql = str_replace(['{tablePrefix}'], [$this->_tbl_prefix], $sql);

        $rows = \Model\R::getAll( $sql, $params );

        if (is_array($data) && isset($data['in_array']) && count($rows) > 0) {
            $items = [];
            foreach ($rows as $i => $row) {
                $items[$row['country_code']] = $row['country_name'];
            }
            return $items;
        }

        return $rows;
    }

    public function getDetail($id)
    {
        $sql = 'SELECT t.*, c.code AS country_code, c.title AS country_name, cp.title AS company_name
            FROM {tablePrefix}ext_hosting_server_location t 
            LEFT JOIN {tablePrefix}country c ON c.id = t.country_id 
            LEFT JOIN {tablePrefix}ext_hosting_company cp ON cp.id = t.hosting_company_id 
            WHERE t.id=:id';

        $params = [ 'id' => $id ];

        $sql = str_replace(['{tablePrefix}'], [$this->_tbl_prefix], $sql);

        $row = \Model\R::getRow( $sql, $params );

        return $row;
    }

    public function deleteNotIn($data = array()) {
        if (count($data) > 0) {
            $list = implode(", ", $data['country_ids']);
            $sql = 'DELETE FROM {tablePrefix}ext_hosting_server_location WHERE hosting_company_id=:hosting_company_id AND country_id NOT IN ('.$list.')';

            $params = [];
            $params['hosting_company_id'] = $data['hosting_company_id'];

            $sql = str_replace(['{tablePrefix}'], [$this->_tbl_prefix], $sql);

            $exec = \Model\R::exec($sql, $params);

            return $exec;
        }

        return false;
    }
}
