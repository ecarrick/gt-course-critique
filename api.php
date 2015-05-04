<?php

include('datalayer.php');

if ($_GET['courseId']) {
  return echo json_encode(courseData($_GET['courseId']));
}
else if ($_GET['profId']) {
  return echo json_encode(profData($_GET['profId']));
}
else {
  return echo json_encode(
    array(
      'err' => 'invalid api call'
    )
  );
}

?>
