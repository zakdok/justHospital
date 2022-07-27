<?php
namespace CzNpay;

/**
 * Class Npay
 *
 * @since 2020.04.06
 *
 * @author Seonmin Choe
 *
 */
class Npay {
  private $dir = '/adm/cz_extend';
  public $pageLength;

  /**
   * function __construct
   *
   * @return void
   */
  public function __construct($pageLength = 30) {
    if ($this->checkIsInstalled() === false) {
      if ($_SERVER["PHP_SELF"] != $this->dir . '/npay.install.php') {
        header("Location: " . $this->dir . "/npay.install.php");
      }
      return;
    }

    $this->pageLength = $pageLength;

    return;
  }


  /**
   * function checkIsInstalled
   *
   * 비급여진료비 테이블이 설치되었는지 체크
   * 
   * @return boolean
   */
  public function checkIsInstalled() {
    $result = sql_query("SHOW TABLES LIKE 'cz_npay'");
    $tableExists = (sql_num_rows($result) > 0) ? true : false;

    return $tableExists;
  }


  /**
   * function install
   *
   * 비급여진료비 테이블 설치
   * 
   * @return void
   */
  public function install() {
    $sql = "CREATE TABLE IF NOT EXISTS `cz_npay` (
      `id` INT NOT NULL AUTO_INCREMENT COMMENT 'id',
      `mb_id` VARCHAR(20) NOT NULL COMMENT '작성자 계정 ID',
      `category` VARCHAR(20) NOT NULL COMMENT '대분류',
      `sub_category` VARCHAR(20) NULL COMMENT '분류',
      `code` VARCHAR(20) NULL COMMENT '코드',
      `name` VARCHAR(50) NOT NULL COMMENT '명칭',
      `price` INT NULL COMMENT '비용',
      `min_price` INT NULL COMMENT '최저비용',
      `max_price` INT NULL COMMENT '최고비용',
      `include_material` VARCHAR(10) NULL COMMENT '치료재료대 포함여부',
      `include_medicine` VARCHAR(10) NULL COMMENT '약제비 포함여부',
      `note` VARCHAR(255) NULL COMMENT '특이사항',
      `change_date` DATE NULL COMMENT '최종변경일',
      `created_at` DATETIME NOT NULL COMMENT '등록일시',
      `created_ip` VARCHAR(15) NOT NULL COMMENT '등록 IP',
      `updated_at` DATETIME NULL COMMENT '수정일시',
      `updated_ip` VARCHAR(15) NULL COMMENT '수정 IP',
      PRIMARY KEY (`id`));";
    sql_query($sql);

    return;
  }


  /**
   * function categoryToText
   *
   * 숫자 카테고리를 텍스트로 변환
   * 
   * @param int category 카테고리 번호
   * 
   * @return string 카테고리 텍스트
   */
  public function categoryToText($category) {
    if ($category < 1 || $category > 7) return false;

    $array = [0 => '전체', 1 => '행위료', 2 => '검사료', 3 => '치료재료', 4 => '약제비', 5 => '제증명', 6=> '기타'];

    return $array[$category];
  }


  /**
   * function getList
   *
   * 목록
   *
   * @param array opt 
   * @param int opt.page 불러올 페이지
   * @param string opt.category 불러올 카테고리
   * @param string opt.searchWord 검색어
   *
   * @return array
   *
   */
  public function getList(array $opt) {
    $page = (!empty($opt['page'])) ? $opt['page'] : 1;
    $category = (!empty($opt['category'])) ? $opt['category'] : "";
    $searchWord = (!empty($opt['searchWord'])) ? $opt['searchWord'] : "";
    $selectStx = (!empty($opt['selectStx'])) ? $opt['selectStx'] : "";

    # redefine
    $searchWord = trim($searchWord);

    # 카테고리 변환
    $categoryText = '';
    if ($category != '') {
      $categoryText = $this->categoryToText($category);
    }

    if ($categoryText === false) {
      return ['success'=>false, 'msg'=>'존재하지 않는 카테고리입니다.'];
    }

    #----- wh

    $wh = "WHERE 1 = 1";

    if ($categoryText != '') {
      $wh .= " AND category like '%{$categoryText}%'";
    }

    if ($searchWord) {
      $wh .= " AND $selectStx like '%{$searchWord}%'";
    }

    #-----/


    if ($page > 0) {
      # get : count
       $sql = "SELECT Count(*) as cnt"
        ."    FROM cz_npay"
        ."    {$wh}"
        ;
      $rs1 = sql_fetch($sql);

      $pa = $this->pageLength;
      $tot = $rs1['cnt'];
      $topa = intval(($tot - 1) / $pa) + 1;

      if ($page < 1) $page = 1;
      if ($page > $topa) $page = $topa;
      $st = $pa * ($page - 1);
    }

    # get
    $sql = "SELECT *"
      ."    FROM cz_npay"
      ."    {$wh}"
      ."    ORDER BY id ASC"
      ."    " . (($page > 0) ? "LIMIT {$st}, {$pa}" : "")
      ;
    $db1 = sql_query($sql);

    $ret = [];
    $ret['itm'] = [];
    if($page > 0) {
      $ret['tot'] = $tot;
      $ret['topa'] = $topa;
      $ret['st'] = $st;
    }
    while ($rs1 = sql_fetch_array($db1))
    {
      $ret['itm'][] = array(
        'id' => $rs1['id']
        , 'category' => $rs1['category']
        , 'sub_category' => $rs1['sub_category']
        , 'code' => $rs1['code']
        , 'name' => $rs1['name']
        , 'price' => $rs1['price']
        , 'min_price' => $rs1['min_price']
        , 'max_price' => $rs1['max_price']
        , 'include_material' => $rs1['include_material']
        , 'include_medicine' => $rs1['include_medicine']
        , 'note' => $rs1['note']
        , 'change_date' => $rs1['change_date']
        , 'created_at' => $rs1['created_at']
        );
    }

    return $ret;
  }


  /**
   * function getListForExcel
   *
   * 전체 목록 엑셀 다운로드
   *
   * @return array
   *
   */
  public function getListForExcel() {
    # get
    $sql = "SELECT *"
      ."    FROM cz_npay"
      ."    ORDER BY id ASC"
      ;
    $db1 = sql_query($sql);

    $ret = [];
    while ($rs1 = sql_fetch_array($db1))
    {
      $ret[] = array(
        'category' => $rs1['category']
        , 'sub_category' => $rs1['sub_category']
        , 'code' => $rs1['code']
        , 'name' => $rs1['name']
        , 'price' => $rs1['price']
        , 'min_price' => $rs1['min_price']
        , 'max_price' => $rs1['max_price']
        , 'include_material' => $rs1['include_material']
        , 'include_medicine' => $rs1['include_medicine']
        , 'note' => $rs1['note']
        , 'created_at' => $rs1['created_at']
        , 'change_date' => $rs1['change_date']
        );
    }

    return $ret;
  }


  /**
   * function read
   *
   * 상세
   *
   * @param array opt 
   * @param int opt.id 비급여 진료비 id
   *
   * @return array
   *
   */
  public function read(array $opt) {
    #----- wh

    $wh = "WHERE id = {$opt['id']}";

    #-----/


    # get
    $sql = "SELECT *"
      ."    FROM cz_npay"
      ."    {$wh}"
      ."    Limit 0, 1"
      ;
    $row = sql_fetch($sql);


    $ret = [];
    if ($row['id'] == '') {
      $ret['success'] = false;
    }
    else {
      $ret['success'] = true;
      $ret['itm'] = $row;
    }


    return $ret;
  }


  /**
   * function excelAdd
   *
   * 엑셀 업로드를 통한 Add
   *
   * @param array line 
   * @param string line[0] category 대분류 
   * @param string line[1] sub_category 분류 
   * @param string line[2] code 코드
   * @param string line[3] name 명칭
   * @param number line[4] price 비용
   * @param number line[5] min_price 최저비용
   * @param number line[6] max_price 최고비용
   * @param string line[7] include_material 치료재료대 포함여부
   * @param string line[8] include_medicine 약제비 포함여부
   * @param string line[9] note 특이사항
   * @param string line[10] change_date 최종변경일
   * @param string line[11] create_at 최초등록일
   *
   * @return void
   *
   */
  public function excelAdd(array $line) {
    global $member;


    // Insert
    $sql = "INSERT INTO cz_npay(
      `mb_id`
      , `category`
      , `sub_category`
      , `code`
      , `name`
      , `price`
      , `min_price`
      , `max_price`
      , `include_material`
      , `include_medicine`
      , `note`
      , `change_date`
      , `created_at`
      , `created_ip`
      ) VALUES (
      '{$member['mb_id']}'
      , '{$line[0]}'
      , '{$line[1]}'
      , '{$line[2]}'
      , '{$line[3]}'
      , '{$line[4]}'
      , '{$line[5]}'
      , '{$line[6]}'
      , '{$line[7]}'
      , '{$line[8]}'
      , '{$line[9]}'
      , '{$line[11]}'
      , '{$line[10]}'
      , '" . $_SERVER['REMOTE_ADDR'] . "'
      )";
    sql_query($sql);


    return;
  }

  /**
   * function setData
   * 
   * 등록
   * 
   * 
   * 
   */
  public function setDataB(array $formData) {
    $sql = "INSERT INTO cz_npay(
      `category`
      , `sub_category`
      , `code`
      , `name`
      , `price`
      , `min_price`
      , `max_price`
      , `include_material`
      , `include_medicine`
      , `note`
      , `change_date`
      , `created_at`
      , `created_ip`
      ) VALUES (
      '{$formData['category']}'
      , '{$formData['sub_category']}'
      , '{$formData['code']}'
      , '{$formData['name']}'
      , '{$formData['price']}'
      , '{$formData['min_price']}'
      , '{$formData['max_price']}'
      , '{$formData['include_material']}'
      , '{$formData['include_medicine']}'
      , '{$formData['note']}'
      , '{$formData['created_at']}'
      , '{$formData['change_date']}'
      , '" . $_SERVER['REMOTE_ADDR'] . "'
      )";
    sql_query($sql);

    $id = sql_insert_id();

    return ['success' => true, 'id' => $id];
  }


  /**
   * function update
   *
   * 수정
   *
   * @param array formData 
   * @param string formData.id 비급여 진료비 id
   * @param string formData.category 대분류 
   * @param string formData.sub_category 증분류 
   * @param string formData.code 코드
   * @param string formData.name 명칭
   * @param number formData.price 비용
   * @param number formData.min_price 최저비용
   * @param number formData.max_price 최고비용
   * @param string formData.include_material 치료재료대 포함여부
   * @param string formData.include_medicine 약제비 포함여부
   * @param string formData.note 특이사항
   * @param string formData.change_date 최종변경일
   *
   * @return void
   *
   */
  public function update(array $formData) {
    if (!$this->checkExistId($formData['id'])) 
      return ['success' => false, 'msg' => '존재하지 않는 데이터입니다.'];

    // Update
    $sql = "UPDATE cz_npay SET 
      `category` = '{$formData['category']}' 
      , `sub_category` = '{$formData['sub_category']}' 
      , `code` = '{$formData['code']}' 
      , `name` = '{$formData['name']}' 
      , `price` = '{$formData['price']}' 
      , `min_price` = '{$formData['min_price']}' 
      , `max_price` = '{$formData['max_price']}' 
      , `include_material` = '{$formData['include_material']}' 
      , `include_medicine` = '{$formData['include_medicine']}' 
      , `note` = '{$formData['note']}' 
      , `change_date` = '{$formData['change_date']}'
      , `created_at` = '{$formData['created_at']}'
      , `updated_at` = now()
      , `updated_ip` = '{$_SERVER['REMOTE_ADDR']}' 
      WHERE id = {$formData['id']}";
    sql_query($sql);


    return ['success'=>true, 'id'=>$formData['id']];
  }


  /**
   * function delete
   *
   * 비급여 진료비 삭제
   *
   * @param int id 비급여 진료비 id
   *
   * @return array
   *
   */
  public function delete(int $id) {
    if (!$this->checkExistId($id)) 
      return ['success' => false, 'msg' => '존재하지 않는 데이터입니다.'];


    // DELETE : 삭제
    $sql = "DELETE FROM cz_npay WHERE id = {$id} LIMIT 1";
    sql_query($sql);


    return ['success' => true, 'msg' => '성공적으로 삭제했습니다.'];
  }


  /**
   * function deleteAll
   *
   * 비급여 진료비 전체삭제
   *
   *
   * @return array
   *
   */
  public function deleteAll() {
    // DELETE : 삭제
    $sql = "DELETE FROM cz_npay";
    sql_query($sql);


    return ['success' => true, 'msg' => '성공적으로 삭제했습니다.'];
  }


  /**
   * function checkExistId
   *
   * 데이터가 존재하는지 확인
   *
   * @param int id 비급여 진료비 id
   *
   * @return boolean
   *
   */
  public function checkExistId(int $id) {
    $id = (is_int($id)) ? $id : '';

    if ($id == '') 
      return false;

    $row = sql_fetch("SELECT count(*) AS cnt FROM `cz_npay` WHERE id = {$id}");
    $ret = ($row['cnt'] <= 0) ? false : true;

    return $ret;
  }
}