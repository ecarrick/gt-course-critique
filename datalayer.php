<?php

include("config.php");

$db = new database;

function courseData($courseId) {
  $courseName = $db->pdo->prepare("SELECT DISTINCT Course FROM Data WHERE courseID = :id");
  $raw = $db->pdo->prepare("SELECT profID, Prof, Size, ROUND(AVG(GPA),2), ROUND(AVG(A)), ROUND(AVG(B)), ROUND(AVG(C)), ROUND(AVG(D)), ROUND(AVG(F)), ROUND(AVG(W)) FROM Data WHERE courseID = :id AND GPA != 0 GROUP BY Prof");
  $avg = $db->pdo->prepare("SELECT ROUND(AVG(GPA),2), ROUND(AVG(A)), ROUND(AVG(B)), ROUND(AVG(C)), ROUND(AVG(D)), ROUND(AVG(F)) FROM Data WHERE courseID = :id AND GPA != 0");

  if (!$courseName->execute(array(":id" => $courseID)) || !$raw->execute(array(":id" => $courseID)) || !$avg->execute(array(":id" => $courseID))) {
    return array( 'err' => 'failed to grab course data', 'courseId' => $courseId);
  }

  $courseName = $courseName->fetch(PDO::FETCH_NUM);
  $courseName = $courseName[0];

  $avg = $avg->fetch(PDO::FETCH_ASSOC);
  $raw = $raw->fetchAll(PDO::FETCH_ASSOC);

  return array(
    'courseId' => $courseId,
    'courseName' => $courseName,
    'grades' => array(
      'avg' => $avg,
      'raw' => $raw
    )
  );
}

function profData($profId) {
  $profName = $db->pdo->prepare("SELECT DISTINCT Prof FROM Data WHERE profID = :id");
  $raw = $db->pdo->prepare("SELECT courseID, Course, Section, Year, Size, GPA, A, B, C, D, F, W FROM Data WHERE profID = :id");
  $avg = $db->pdo->prepare("SELECT ROUND(AVG(GPA),2), ROUND(AVG(A)), ROUND(AVG(B)), ROUND(AVG(C)), ROUND(AVG(D)), ROUND(AVG(F)) FROM Data WHERE profID = :id AND GPA != 0 GROUP BY Prof");
  //execute query and handle error
  if (!$raw->execute(array(":id" => $profID)) || !$avg->execute(array(":id" => $profID)) || !$profName->execute(array(":id" => $profID))) {
    return array('err'=>'failed to grab prof data', 'profId'=>$profId);
  }

  $profName = $profName->fetch(PDO::FETCH_NUM);
  $profName = $profName[0];

  $avg = $avg->fetch(PDO::FETCH_ASSOC);
  $raw = $raw->fetchAll(PDO::FETCH_ASSOC);

  return array(
    'profId' => $profId,
    'profName' => $profName,
    'grades' => array(
      'avg'  => $avg,
      'raw' => $raw
    )
  );
}
