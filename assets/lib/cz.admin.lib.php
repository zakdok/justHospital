<?php
  # short : echo json
  function echo_json($outData, $charset="UTF-8")
  {
    header( "Content-type: application/json; charset={$charset}" );
    echo json_encode($outData); exit();
  }
?>