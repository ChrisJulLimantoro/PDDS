<?php

require_once 'autoload.php';

$client = new MongoDB\Client();
$resto = $client->Ceje->restaurants;

// Get distinct boroughs in dataset
$cursor = $resto->distinct('borough');
// var_dump($cursor);

// Find all restaurants in Staten Island
$cursor = $resto->find(
  [
    'borough' => 'Staten Island',
  ],
  [
    'projection' => [
      '_id' => 0,
    ],
  ]
);

// foreach ($cursor as $doc) {
//   echo "{\n";
//   foreach ($doc as $key => $value) {
//     if (is_string($value)) {
//       echo "\t$key: $value\n";
//     }
// /*
//     else if (is_object($value) || is_array($value)) {
//       echo "  $key => ";
//       var_dump($value);
//       echo "\n";
//     }
// */
//   }
//   echo "}\n";
// }

// var_dump($cursor->toArray());
echo "<br><br>\n";

// Find all restaurants receiving grades.score > 70
$cursor = $resto->find(  
  [
    '$and' => [
      ['borough' => 'Bronx'],
      ['cuisine' => ['$regex' => '/'.'bake'.'/i']]
    ],
  ],
  [
    'projection' => [
      '_id' => 0,
    ]
  ]
);
$data = [];
foreach($cursor as $doc){
  $temp = [];
  foreach($doc as $key => $value){
    if($key === 'address'){
      $add = [];
      foreach($value as $k =>$v){
        if($k === 'coord') continue;
        $add[$k] = $v;
      }
      $temp['address'] = $add['building'].', '.$add['street'];
    }else if($key === 'grades'){
      // Only display the lowest grade
      $temp['score'] = $value[0]['score'];
    }else{
      $temp[$key] = $value;
    }
  }
  $data[] = $temp;
}
var_dump($data);
// foreach ($cursor as $doc) {
//   echo "{\n";
//   foreach ($doc as $key => $value) {
//     if (is_string($value)) {
//       echo "\t$key: $value\n";
//     }
//     if ($key === "grades") {
//       echo "\t$key => \n";
//       foreach ($value as $grade) {
//         echo "\t\t{\n";
//         echo "\t\t\tdate: {$grade['date']}\n";
//         echo "\t\t\tgrade: {$grade['grade']}\n";
//         echo "\t\t\tscore: {$grade['score']}\n";
//         echo "\t\t}\n";
//       }
//     }
//   }
//   echo "}\n";
// }
// var_dump($cursor->toArray());
?>