<?php
    require_once 'autoload.php';

    $client = Laudis\Neo4j\ClientBuilder::create()
        ->withDriver('default', 'neo4j://neo4j:password@localhost')
        ->build();
    
    if(isset($_POST['filter'])){
        $filter = $_POST['filter'];
        // var_dump($filter);
        $filter = str_replace("'","\\'",$filter);
        // var_dump($filter);
        $result = $client->run(<<<CYPHER
        MATCH (s1:Supplier)-[:SUPPLIES]->(p1:Product)-[:PART_OF]->(c:Category)<-[:PART_OF]-(p2:Product)<-[:SUPPLIES]-(s2:Supplier)
        WHERE s1.companyName = '$filter' AND s2.companyName <> '$filter'
        RETURN s2.companyName as Competitor, count(s2) as NoProducts
        ORDER BY NoProducts DESC
        CYPHER, ['dbName' => 'neo4j']);
        
        $data = [];
        $header = ['No','Competitor','NoProducts'];
        $no = 1;
        foreach($result as $res){
            $data[] = [$no++,$res->get('Competitor'),$res->get('NoProducts')];
        }
        if($data == []){
            echo json_encode(['header' => 'data','data' => 'No data with that company name!']);
        }else{
            echo json_encode(['header' => $header,'data' => $data]);
        }
        // var_dump($data);
        exit;
    }
    $dropdown = $client->run(<<<CYPHER
    MATCH (c:Supplier) RETURN c.companyName as CompanyName
    CYPHER, ['dbName' => 'neo4j']);
    $company = [];
    foreach($dropdown as $drop){
        $company[] = $drop->get('CompanyName');
    }
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <!-- CDN for jquery -->
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.0/jquery.min.js"></script>
        <!-- CDN for Tailwind -->
        <script src="https://cdn.tailwindcss.com/3.3.0"></script>
        <script src="https://cdn.tailwindcss.com?plugins=forms,typography,aspect-ratio,line-clamp"></script>
        <!-- CDN for Tailwind Element -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tw-elements/dist/css/tw-elements.min.css">
        <!-- CDN for SweetAlert -->
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <link
            href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700,900&display=swap"
            rel="stylesheet" />

        <style>
        @import url('https://fonts.googleapis.com/css2?family=Spinnaker&display=swap');
        *{font-family : 'Spinnaker',sans-serif !important}
        </style>


    </head>
    <body class="w-screen min-h-screen h-full pb-16 bg-slate-300 pt-16 justify-center items-center overflow-x-hidden overflow-y-auto">
        <div class="flex flex-col w-3/4 mx-auto">
            <div class="text-center mb-5">
                <h2 class="text-7xl text-white font-bold">Neo4j - PDDS - LAB 06</h2>
                <h4 class="text-4xl text-white">Christopher Julius</h4>
                <h4 class="text-4xl text-white">C14210073</h4>
            </div>  
        </div>
        <div class="w-3/4 shadow-2xl bg-white rounded-xl mx-auto py-8 pb-5">
            <div class="px-8 justify-center items-center">
                <div class="mb-3 mx-auto w-3/4">
                    <div class="relative mb-4 flex w-full flex-wrap items-stretch">
                        <select data-te-select-init id="company" class="w-full">
                            <option value="test">Select Company</option>
                            <?php
                                foreach($company as $comp){
                                    // var_dump($comp);
                                    echo '<option value="'.$comp.'">'.$comp.'</option>';
                                }
                            ?>
                        </select>
                        <label data-te-select-label-ref class="w-full">Company Name</label>
                    </div>
                </div>
                <div id="datatable" data-te-fixed-header="true" data-te-width="300" data-te-clickable-rows= "true"></div>
            </div>
        </div>
    </body>
    <script src="https://cdn.jsdelivr.net/npm/tw-elements/dist/js/tw-elements.umd.min.js"></script>
    <script>
        $(document).ready(function(){
            let instance;
            function ajaxCall(filt = null){
                if(filt == null || filt == '' || filt == 'test') {
                    if(!instance){
                        instance = new te.Datatable(document.getElementById('datatable'), {
                            columns: ['data'],
                            rows: [['No data available at the moment!']],
                        });
                    }else{
                        instance.update({
                            columns: ['data'],
                            rows: [['No data available at the moment!']],
                        });
                    }
                }else{
                    $.ajax({
                        method : 'POST',
                        data : {
                            filter : filt
                        },
                        success : function(response){
                            let res = JSON.parse(response);
                            // console.log(res);
                            let data = {
                                columns: res.header,
                                rows: res.data,
                            };
                            // console.log(data)
                            if(!instance){
                                instance = new te.Datatable(document.getElementById('datatable'), data)
                            }else{
                                instance.update(data);
                            }
                        }
                    });
                }
            }
            ajaxCall(null);
            $("#company").on('change',function(){
                // console.log($(this).val());
                ajaxCall($(this).val());
            })
        });
    </script>
</html>