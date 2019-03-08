<?php
namespace ExtensionsModel;

require_once __DIR__ . '/../../../models/base.php';

class HostingExpertReviewModel extends \Model\BaseModel
{
    const STATUS_PENDING = 'pending';
    const STATUS_PUBLISHED = 'published';
    const STATUS_HIDDEN = 'hidden';

    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'ext_hosting_expert_review';
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
        $sql = 'SELECT t.*, r.name AS expert_name, r.email AS expert_email, r.image   
            FROM {tablePrefix}ext_hosting_expert_review t 
            LEFT JOIN {tablePrefix}ext_hosting_expert r ON r.id = t.expert_id 
            WHERE 1';

        $sql .= ' AND t.status=:status';
        $params = [ 'status' => self::STATUS_PUBLISHED ];
        if (is_array($data)) {
            if (!empty($data['hosting_company_id'])) {
                $sql .= ' AND t.hosting_company_id=:hosting_company_id';
                $params['hosting_company_id'] = $data['hosting_company_id'];
            }
            if (isset($data['status'])) {
                $param['status'] = $data['status'];
            }
        }

        $sql .= ' ORDER BY t.created_at DESC';

        if (isset($data['limit'])) {
            $sql .= ' LIMIT '. $data['limit'];
        }

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
        $sql = 'SELECT t.*, r.name AS expert_name, r.email AS expert_email 
            FROM {tablePrefix}ext_hosting_expert_review t 
            LEFT JOIN {tablePrefix}ext_hosting_expert r ON r.id = t.expert_id 
            WHERE t.id =:id';

        $sql = str_replace(['{tablePrefix}'], [$this->_tbl_prefix], $sql);

        $row = \Model\R::getRow( $sql, ['id'=>$id] );

        return $row;
    }

    public function getStatuses()
    {
        return [
            self::STATUS_PENDING => 'Pending',
            self::STATUS_PUBLISHED => 'Published',
            self::STATUS_HIDDEN => 'Hidden'
        ];
    }

    public function getLastData($data = null)
    {
        $sql = 'SELECT t.*, r.name AS expert_name, r.email AS expert_email, r.image
            FROM {tablePrefix}ext_hosting_expert_review t 
            LEFT JOIN {tablePrefix}ext_hosting_expert r ON r.id = t.expert_id 
            WHERE 1';

        $params = [];
        if (is_array($data)) {
            if (!empty($data['hosting_company_id'])) {
                $sql .= ' AND t.hosting_company_id=:hosting_company_id';
                $params['hosting_company_id'] = $data['hosting_company_id'];
            }
        }

        $sql .= ' ORDER BY t.created_at DESC LIMIT 1';

        $sql = str_replace(['{tablePrefix}'], [$this->_tbl_prefix], $sql);

        $row = \Model\R::getRow( $sql, $params );

        if (!empty($row['id'])) {
            $params2 = ['expert_review_id' => $row['id']];
            $sql2 = 'SELECT c.segment_id, s.title AS segment_title, c.title AS review_title, c.content AS review_content 
              FROM {tablePrefix}ext_hosting_expert_review_content c 
              LEFT JOIN {tablePrefix}ext_hosting_expert_review_segment s ON s.id = c.segment_id 
              WHERE c.expert_review_id =:expert_review_id';
            $sql2 = str_replace(['{tablePrefix}'], [$this->_tbl_prefix], $sql2);

            $rows = \Model\R::getAll( $sql2, $params2 );
            if (count($rows) > 0) {
                $row['content'] = $rows;
            }
        }

        return $row;
    }

    public function getRate($data = null)
    {
        $sql = 'SELECT AVG(r.value) AS average, COUNT(t.expert_id) AS tot_reviewer   
            FROM {tablePrefix}ext_hosting_expert_review t 
            JOIN {tablePrefix}ext_hosting_expert_rate r ON r.review_id = t.id 
            WHERE 1';

        $params = [];
        if (is_array($data)) {
            if (isset($data['hosting_company_id'])) {
                $sql .= ' AND t.hosting_company_id=:hosting_company_id';
                $params['hosting_company_id'] = $data['hosting_company_id'];
            }
        }

        $sql .= ' ORDER BY t.created_at DESC LIMIT 1';

        $sql = str_replace(['{tablePrefix}'], [$this->_tbl_prefix], $sql);

        $row = \Model\R::getRow( $sql, $params );

        return $row;
    }

    public function getRateCategory($data = null)
    {
        $sql = 'SELECT r.category_id, c.title AS category_name, c.configs AS category_configs, 
            AVG(r.value) AS average, COUNT(t.expert_id) AS tot_reviewer   
            FROM {tablePrefix}ext_hosting_expert_review t 
            JOIN {tablePrefix}ext_hosting_expert_rate r ON r.review_id = t.id 
            LEFT JOIN {tablePrefix}ext_hosting_rate_category c ON c.id = r.category_id 
            WHERE 1';

        $params = [];
        if (is_array($data)) {
            if (isset($data['hosting_company_id'])) {
                $sql .= ' AND t.hosting_company_id=:hosting_company_id';
                $params['hosting_company_id'] = $data['hosting_company_id'];
            }
        }

        $sql .= ' GROUP BY r.category_id ORDER BY t.created_at DESC';

        $sql = str_replace(['{tablePrefix}'], [$this->_tbl_prefix], $sql);

        $rows = \Model\R::getAll( $sql, $params );

        return $rows;
    }

    public function getContents($data = null)
    {
        $sql = 'SELECT t.hosting_company_id, t.rate, t.expert_id, c.id AS content_id, c.title, c.content, c.segment_id    
            FROM {tablePrefix}ext_hosting_expert_review t 
            RIGHT JOIN {tablePrefix}ext_hosting_expert_review_content c ON c.expert_review_id = t.id 
            WHERE 1';

        $params = [];
        if (is_array($data)) {
            if (isset($data['hosting_company_id'])) {
                $sql .= ' AND t.hosting_company_id=:hosting_company_id';
                $params['hosting_company_id'] = $data['hosting_company_id'];
            }
            if (isset($data['expert_id'])) {
                $sql .= ' AND t.expert_id=:expert_id';
                $params['expert_id'] = $data['expert_id'];
            }
        }

        $sql .= ' ORDER BY t.created_at DESC';

        $sql = str_replace(['{tablePrefix}'], [$this->_tbl_prefix], $sql);

        $rows = \Model\R::getAll( $sql, $params );

        $items = [];
        foreach ($rows as $i => $row) {
            $items[$row['segment_id']] = $row;
        }

        return $items;
    }
}
