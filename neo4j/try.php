<?php
    require_once 'autoload.php';

    $client = Laudis\Neo4j\ClientBuilder::create()
        ->withDriver('default', 'neo4j://neo4j:password@localhost')
        ->build();
    
    $result = $client->run(<<<'CYPHER'
    MATCH (s1:Supplier)-[:SUPPLIES]->(p1:Product)-[:PART_OF]->(c:Category)<-[:PART_OF]-(p2:Product)<-[:SUPPLIES]-(s2:Supplier)
    WHERE s1.companyName = "Pavlova"
    RETURN s2.companyName as Competitor,c.categoryName as CategoryName, count(s2) as NoProducts
    ORDER BY NoProducts DESC
    CYPHER, ['dbName' => 'neo4j']);

    foreach($result as $person){
        echo $person->get('Competitor')." - Competitor</br>";
        echo $person->get('CategoryName')." - CategoryName</br>";
        echo $person->get('NoProducts')." - NoProducts</br>";
    }
?>
